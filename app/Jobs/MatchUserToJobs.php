<?php

namespace App\Jobs;

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

            $userProfile = [
                'disability_condition' => $profile->disability_condition ?? [],
                'skills' => $profile->skills ?? [],
                'communication_preference' => $profile->communication_preference ?? [],
                'work_environment' => $profile->work_environment ?? [],
            ];

            $jobs = JobVacancyData::all();
            $filteredJobs = $this->preFilter($userProfile, $jobs);

            Log::info("MatchUserToJobs: user {$uid} — {$jobs->count()} jobs → " . count($filteredJobs) . " after pre-filter");

            $batches = array_chunk($filteredJobs, 10);

            if (empty($batches)) {
                Cache::put($statusKey, 'completed', now()->addHours(24));
                Cache::forget($doneKey);
                Cache::forget($totalKey);

                return;
            }

            Cache::put($statusKey, 'processing', now()->addMinutes(10));
            Cache::put($doneKey, 0, now()->addMinutes(10));
            Cache::put($totalKey, count($batches), now()->addMinutes(10));

            \App\Models\JobUserMatch::where('user_id', $uid)->delete();

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
            'remote' => ['Remote', 'WFH', 'Work From Home'],
            'hybrid' => ['Hybrid'],
            'onsite' => ['On-site', 'On site', 'Full time', 'Full-time', 'Fulltime'],
        ];

        $preferredEnvs = [];
        foreach ($userProfile['work_environment'] ?? [] as $env) {
            $env = strtolower($env);
            $preferredEnvs = array_merge($preferredEnvs, $workEnvMap[$env] ?? []);
        }

        $userSkills = array_filter(array_map(function ($s) {
            $s = strtolower(trim($s));

            return strlen($s) >= 3 ? $s : null;
        }, $userProfile['skills'] ?? []));

        $filtered = $jobs->filter(function ($job) use ($preferredEnvs, $userSkills) {
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

            return $workMatch || $skillMatch;
        });

        return $filtered->values()->toArray();
    }
}
