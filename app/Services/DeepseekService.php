<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepseekService
{
    protected string $baseUrl = 'https://api.deepseek.com/v1';

    protected string $model;

    protected int $threshold;

    public function __construct()
    {
        $this->model = config('services.deepseek.model', 'deepseek-chat');
        $this->threshold = (int) config('services.deepseek.match_threshold', 60);
    }

    public function matchUserToJobs(array $userProfile, array $jobs): array
    {
        if (empty($jobs)) {
            return [];
        }

        $prompt = $this->buildMatchingPrompt($userProfile, $jobs);

        $response = Http::retry(3, 2000)
            ->timeout($this->model === 'deepseek-reasoner' ? 120 : 45)
            ->withToken(config('services.deepseek.api_key'))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'temperature' => 0.3,
                'max_tokens' => 4096,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert job matching system for people with disabilities. You assess how well job vacancies match a candidate profile, paying special attention to disability compatibility. Always respond with valid JSON only.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if (! $response->successful()) {
            Log::error('Deepseek API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $data = $response->json();

        $content = $data['choices'][0]['message']['content'] ?? '';

        $result = json_decode($content, true);

        if (! is_array($result)) {
            Log::error('Deepseek invalid JSON response', ['content' => $content]);

            return [];
        }

        return $result['matches'] ?? [];
    }

    protected function buildMatchingPrompt(array $profile, array $jobs): string
    {
        $jobList = '';
        foreach ($jobs as $i => $job) {
            $jobList .= "Job {$i}: [id: {$job['id']}]\n";
            $jobList .= "- Title: {$job['job_title']}\n";
            $jobList .= "- Category: {$job['job_category']}\n";
            $jobList .= "- Skills required: {$job['skill_req']}\n";
            $jobList .= "- Education: {$job['education_req']}\n";
            $jobList .= "- Work type: {$job['work_type']}\n";
            $jobList .= "- Location: {$job['location']}\n";
            $jobList .= "- Description: " . mb_substr($job['jobdesk'], 0, 800) . "\n\n";
        }

        $disability = implode(', ', $profile['disability_condition'] ?? []);
        $skills = implode(', ', $profile['skills'] ?? []);
        $communication = implode(', ', $profile['communication_preference'] ?? []);
        $workEnv = implode(', ', $profile['work_environment'] ?? []);

        return <<<PROMPT
You are matching a candidate with disabilities to job vacancies. Evaluate each job carefully.

=== CANDIDATE PROFILE ===
Disability condition: {$disability}
Skills: {$skills}
Communication preference: {$communication}
Preferred work environment: {$workEnv}

=== WEIGHTING ===
Match scores (0-100) should use these weights:
- Disability fit (35%): Can this person with their disability actually perform this job based on the job description?
- Skill match (30%): Do their skills align with the required skills?
- Work environment (20%): Does the work type (Remote/Hybrid/On-site/Full-time/Contract) match their preference?
- Communication (10%): Does the job's communication demands match their preference (text/verbal/sign language)?
- Education (5%): Does their education level match requirements?

=== SCORING GUIDE ===
0-20: Not compatible at all
21-40: Poor match
41-60: Somewhat compatible
61-80: Good match
81-100: Excellent match

=== RESPONSE FORMAT ===
Return ONLY a JSON object with this exact structure:
{
  "matches": [
    {
      "id": <job_id>,
      "match_score": <0-100>,
      "is_match": <true if score >= {$this->threshold}>,
      "disability_score": <0-100>,
      "skill_score": <0-100>,
      "environment_score": <0-100>,
      "communication_score": <0-100>,
      "education_score": <0-100>,
      "match_reason": "<1-2 sentence summary in Bahasa Indonesia>"
    }
  ]
}

=== JOB LISTINGS ===
{$jobList}
PROMPT;
    }
}
