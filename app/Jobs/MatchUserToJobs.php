<?php

namespace App\Jobs;

use App\Data\OnboardingData;
use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MatchUserToJobs
{
    public function __construct(
        protected int $userId
    ) {}

    public function handle(): void
    {
        $uid = $this->userId;
        $statusKey = "matching_status_{$uid}";
        $doneKey = "matching_done_{$uid}";
        $totalKey = "matching_total_{$uid}";

        try {
            $user = User::with('profile')->find($uid);

            if (! $user || ! $user->profile) {
                Log::warning("MatchUserToJobs: user or profile not found for ID {$uid}");
                Cache::put($statusKey, 'failed', now()->addHours(1));

                return;
            }

            $profile = $user->profile;
            $dateOfBirth = $user->date_of_birth;

            $skillCategories = $profile->skill_categories ?? [];
            if ($skillCategories && isset($skillCategories[0]) && is_array($skillCategories[0])) {
                $selectedCats = array_column($skillCategories, 'category');
                $selectedSubs = [];
                foreach ($skillCategories as $entry) {
                    foreach ($entry['subs'] ?? [] as $sub) {
                        $selectedSubs[] = $sub;
                    }
                }
            } else {
                $selectedCats = $skillCategories;
                $selectedSubs = [];
            }

            $userProfile = [
                'disability_condition' => $profile->disability_condition ?? [],
                'hearing_level' => $profile->hearing_level ?? '',
                'communication_preference' => $profile->communication_preference ?? [],
                'work_environment' => $profile->work_environment ?? [],
                'skill_categories' => $selectedCats,
                'sub_skills' => $selectedSubs,
                'education_level' => $profile->education_level ?? '',
                'education_major' => $profile->education_major ?? '',
                'job_types' => $profile->job_types ?? [],
                'preferred_locations' => $profile->preferred_locations ?? [],
                'age' => $dateOfBirth ? $dateOfBirth->age : null,
            ];

            $jobs = JobVacancyData::all();
            $filteredJobs = $this->preFilter($userProfile, $jobs);

            Log::info("MatchUserToJobs: user {$uid} — {$jobs->count()} jobs → ".count($filteredJobs).' after pre-filter');

            $batches = array_chunk($filteredJobs, 15);

            if (empty($batches)) {
                Cache::put($statusKey, 'completed', now()->addHours(24));
                Cache::forget($doneKey);
                Cache::forget($totalKey);

                return;
            }

            Cache::put($statusKey, 'processing', now()->addMinutes(10));
            Cache::put($doneKey, 0, now()->addMinutes(10));
            Cache::put($totalKey, count($batches), now()->addMinutes(10));

            JobUserMatch::where('user_id', $uid)->delete();

            foreach ($batches as $i => $batch) {
                MatchBatchJob::dispatch($uid, $userProfile, $batch, $i);
            }

        } catch (\Throwable $e) {
            Log::error("MatchUserToJobs failed for user {$uid}: {$e->getMessage()}");
            Cache::put($statusKey, 'failed', now()->addHours(1));
            Cache::forget($doneKey);
            Cache::forget($totalKey);

            throw $e;
        }
    }

    protected function preFilter(array $userProfile, $jobs): array
    {

        $workEnvMap = [
            'remote' => ['Remote', 'WFH', 'Work From Home', 'Kerja dari rumah'],
            'hybrid' => ['Hybrid'],
            'onsite_juru_isyarat' => ['On-site', 'On site', 'Full time', 'Full-time', 'Fulltime', 'Kontrak/Temporer'],
            'onsite_notifikasi_visual' => ['On-site', 'On site', 'Full time', 'Full-time', 'Fulltime', 'Kontrak/Temporer'],
            'onsite_standar' => ['On-site', 'On site', 'Full time', 'Full-time', 'Fulltime', 'Kontrak/Temporer'],
        ];

        $preferredEnvs = [];
        foreach ($userProfile['work_environment'] ?? [] as $env) {
            $env = strtolower($env);
            $preferredEnvs = array_merge($preferredEnvs, $workEnvMap[$env] ?? []);
        }

        $userSkills = [];
        $skillCategoryLabels = [];
        foreach ($userProfile['skill_categories'] ?? [] as $cat) {
            $label = OnboardingData::SKILL_CATEGORIES[$cat] ?? $cat;
            $skillCategoryLabels[] = $label;
            $words = explode(' ', strtolower($label));
            foreach ($words as $word) {
                if (strlen($word) >= 3) {
                    $userSkills[] = $word;
                }
            }
        }
        foreach ($userProfile['sub_skills'] ?? [] as $sub) {
            foreach (OnboardingData::SKILL_SUBS as $cat => $subs) {
                $label = $subs[$sub] ?? null;
                if ($label) {
                    $words = explode(' ', strtolower($label));
                    foreach ($words as $word) {
                        if (strlen($word) >= 3) {
                            $userSkills[] = $word;
                        }
                    }
                    break;
                }
            }
        }
        $userSkills = array_unique($userSkills);

        $preferredLocations = $userProfile['preferred_locations'] ?? [];
        $locationLabels = [];
        foreach ($preferredLocations as $loc) {
            $locationLabels[] = OnboardingData::LOCATIONS[$loc] ?? $loc;
        }

        $filtered = $jobs->filter(function ($job) use ($preferredEnvs, $userSkills, $locationLabels, $userProfile) {
            if (! $this->passesEducationFilter($userProfile['education_level'] ?? '', $job->education_req)) {
                return false;
            }

            $workMatch = false;
            if (empty($preferredEnvs)) {
                $workMatch = true;
            } else {
                foreach ($preferredEnvs as $term) {
                    if (stripos($job->work_type ?? '', $term) !== false) {
                        $workMatch = true;
                        break;
                    }
                }
            }

            $skillMatch = false;
            if (empty($userSkills) || empty($job->skill_req)) {
                $skillMatch = empty($userSkills);
            } else {
                $reqLower = strtolower($job->skill_req);
                foreach ($userSkills as $skill) {
                    if (str_contains($reqLower, $skill)) {
                        $skillMatch = true;
                        break;
                    }
                }
            }

            $locationMatch = false;
            if (empty($locationLabels)) {
                $locationMatch = true;
            } else {
                foreach ($locationLabels as $loc) {
                    if (! empty($job->location) && stripos($job->location, $loc) !== false) {
                        $locationMatch = true;
                        break;
                    }
                }
            }

            return $workMatch || $skillMatch || $locationMatch;
        });

        return $filtered->values()->toArray();
    }

    protected function passesEducationFilter(string $userLevel, ?string $jobEducationReq): bool
    {
        if (empty($jobEducationReq) || stripos($jobEducationReq, 'Tidak Disebutkan') !== false) {
            return true;
        }

        $levels = [
            'sd' => 1, 'smp' => 2, 'sma' => 3, 'smk' => 3,
            'd1' => 4, 'd2' => 4, 'd3' => 4, 'd4' => 4, 'diploma' => 4,
            's1' => 5, 'sarjana' => 5, 'strata 1' => 5, 'strata satu' => 5, 'bachelor' => 5, 'degree' => 5,
            'profesi' => 6,
            's2' => 7, 'magister' => 7, 'master' => 7, 'strata 2' => 7, 'strata dua' => 7,
            's3' => 8, 'doktor' => 8, 'doctor' => 8, 'phd' => 8,
        ];

        $userLevelValue = $levels[strtolower($userLevel)] ?? 0;
        if ($userLevelValue === 0) {
            return true;
        }

        $highestRequired = 0;
        foreach ($levels as $keyword => $value) {
            if (stripos($jobEducationReq, $keyword) !== false) {
                $highestRequired = max($highestRequired, $value);
            }
        }

        if ($highestRequired === 0) {
            return true;
        }

        return $userLevelValue >= $highestRequired;
    }
}
