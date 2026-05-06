<?php

namespace App\Jobs;

use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
use App\Services\DeepseekService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MatchBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 3;

    public function __construct(
        protected int $userId,
        protected array $userProfile,
        protected array $jobs,
        protected int $batchIndex,
    ) {}

    public function handle(DeepseekService $ai): void
    {
        $uid = $this->userId;
        $statusKey = "matching_status_{$uid}";
        $doneKey = "matching_done_{$uid}";
        $totalKey = "matching_total_{$uid}";

        try {
            $results = $ai->matchUserToJobs($this->userProfile, $this->jobs);

            $jobIds = array_column($this->jobs, 'id');
            $existingJobs = JobVacancyData::whereIn('id', $jobIds)->pluck('id')->toArray();

            foreach ($results as $result) {
                $jobId = (int) ($result['id'] ?? 0);
                if (! $jobId || ! in_array($jobId, $existingJobs)) {
                    continue;
                }

                JobUserMatch::updateOrCreate(
                    [
                        'job_vacancy_data_id' => $jobId,
                        'user_id' => $uid,
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

            $done = Cache::increment($doneKey);
            $total = (int) Cache::get($totalKey, 0);

            if ($done >= $total) {
                Cache::put($statusKey, 'completed', now()->addHours(24));
                Cache::forget($doneKey);
                Cache::forget($totalKey);
            }

        } catch (\Throwable $e) {
            Log::error("MatchBatchJob failed — user {$uid}, batch {$this->batchIndex}: {$e->getMessage()}");

            $done = (int) Cache::get($doneKey, 0);
            $total = (int) Cache::get($totalKey, 0);

            if ($done >= $total) {
                Cache::put($statusKey, 'completed', now()->addHours(24));
            }

            throw $e;
        }
    }
}
