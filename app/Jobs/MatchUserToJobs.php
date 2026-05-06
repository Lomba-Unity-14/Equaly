<?php

namespace App\Jobs;

use App\Models\JobVacancyData;
use App\Models\User;
use App\Services\DeepseekService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MatchUserToJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 5;

    public function __construct(
        protected int $userId
    ) {}

    public function handle(DeepseekService $ai): void
    {
        $cacheKey = "matching_status_{$this->userId}";

        try {
            $user = User::with('profile')->find($this->userId);

            if (! $user || ! $user->profile) {
                Log::warning("MatchUserToJobs: user or profile not found for ID {$this->userId}");
                Cache::put($cacheKey, 'failed', now()->addHours(1));

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

            Cache::put($cacheKey, 'processing', now()->addMinutes(10));

            $batches = array_chunk($filteredJobs, 5);

            foreach ($batches as $batch) {
                $results = $ai->matchUserToJobs($userProfile, $batch);

                foreach ($results as $result) {
                    if (empty($result['id'])) {
                        continue;
                    }

                    $jobId = (int) $result['id'];

                    $jobExists = $jobs->firstWhere('id', $jobId);
                    if (! $jobExists) {
                        continue;
                    }

                    \App\Models\JobUserMatch::updateOrCreate(
                        [
                            'job_vacancy_data_id' => $jobId,
                            'user_id' => $this->userId,
                        ],
                        [
                            'match_score' => (int) ($result['match_score'] ?? 0),
                            'is_match' => ! empty($result['is_match']),
                            'disability_score' => (int) ($result['disability_score'] ?? 0),
                            'skill_score' => (int) ($result['skill_score'] ?? 0),
                            'environment_score' => (int) ($result['environment_score'] ?? 0),
                            'communication_score' => (int) ($result['communication_score'] ?? 0),
                            'education_score' => (int) ($result['education_score'] ?? 0),
                            'match_reason' => $result['match_reason'] ?? null,
                            'calculated_at' => now(),
                        ]
                    );
                }
            }

            Cache::put($cacheKey, 'completed', now()->addHours(24));

        } catch (\Throwable $e) {
            Log::error("MatchUserToJobs failed for user {$this->userId}: {$e->getMessage()}");
            Cache::put($cacheKey, 'failed', now()->addHours(1));

            throw $e;
        }
    }

    protected function preFilter(array $userProfile, $jobs): array
    {
        $workEnvMap = [
            'remote' => ['Remote', 'WFH', 'Work From Home'],
            'hybrid' => ['Hybrid'],
            'onsite' => ['On-site', 'On site', 'Full time'],
        ];

        $preferredEnvs = [];
        foreach ($userProfile['work_environment'] ?? [] as $env) {
            $env = strtolower($env);
            $preferredEnvs = array_merge($preferredEnvs, $workEnvMap[$env] ?? []);
        }

        if (empty($preferredEnvs)) {
            return $jobs->toArray();
        }

        $filtered = $jobs->filter(function ($job) use ($preferredEnvs) {
            foreach ($preferredEnvs as $term) {
                if (stripos($job->work_type ?? '', $term) !== false) {
                    return true;
                }
            }

            return false;
        });

        return $filtered->values()->toArray();
    }
}
