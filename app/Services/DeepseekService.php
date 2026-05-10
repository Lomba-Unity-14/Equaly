<?php

namespace App\Services;

use App\Data\OnboardingData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepseekService
{
    protected string $baseUrl = 'https://api.deepseek.com/v1';

    protected string $model;

    protected int $threshold;

    public function __construct()
    {
        $this->model = config('services.deepseek.model', 'deepseek-v4-pro');
        $this->threshold = (int) config('services.deepseek.match_threshold', 60);
    }

    public function matchUserToJobs(array $userProfile, array $jobs): array
    {
        if (empty($jobs)) {
            return [];
        }

        $prompt = $this->buildMatchingPrompt($userProfile, $jobs);

        $payload = [
            'model' => $this->model,
            'temperature' => 0.0,
            'max_tokens' => 4096,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert job matching system for deaf/hard-of-hearing individuals in Indonesia. Always respond with valid JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        if ($this->model === 'deepseek-v4-pro') {
            $payload['thinking'] = ['type' => 'disabled'];
        }

        $response = Http::retry(3, 2000)
            ->timeout(in_array($this->model, ['deepseek-reasoner', 'deepseek-v4-pro']) ? 120 : 45)
            ->withToken(config('services.deepseek.api_key'))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/chat/completions", $payload);

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
            $jobList .= '- Description: '.mb_substr($job['jobdesk'], 0, 800)."\n\n";
        }

        $disability = implode(', ', $profile['disability_condition'] ?? []);
        $hearingLabel = OnboardingData::HEARING_LEVELS[$profile['hearing_level'] ?? ''] ?? ($profile['hearing_level'] ?? '');

        $communicationLabels = [];
        foreach ($profile['communication_preference'] ?? [] as $comm) {
            $communicationLabels[] = OnboardingData::COMMUNICATION_PREFERENCES[$comm] ?? $comm;
        }
        $communication = implode(', ', $communicationLabels);

        $workEnvLabels = [];
        foreach ($profile['work_environment'] ?? [] as $env) {
            $workEnvLabels[] = OnboardingData::WORK_ENVIRONMENTS[$env] ?? $env;
        }
        $workEnv = implode(', ', $workEnvLabels);

        $skillCategoryLabels = [];
        foreach ($profile['skill_categories'] ?? [] as $cat) {
            $skillCategoryLabels[] = OnboardingData::SKILL_CATEGORIES[$cat] ?? $cat;
        }
        $skillCats = implode(', ', $skillCategoryLabels);

        $subSkillLabels = [];
        foreach ($profile['sub_skills'] ?? [] as $sub) {
            foreach (OnboardingData::SKILL_SUBS as $cat => $subs) {
                $label = $subs[$sub] ?? null;
                if ($label) {
                    $subSkillLabels[] = $label;
                    break;
                }
            }
        }
        $subSkills = implode(', ', $subSkillLabels);

        $educationLabel = OnboardingData::EDUCATION_LEVELS[$profile['education_level'] ?? ''] ?? ($profile['education_level'] ?? '');
        $education = $educationLabel;
        if (! empty($profile['education_major'])) {
            $education .= ' — '.$profile['education_major'];
        }

        $jobTypeLabels = [];
        foreach ($profile['job_types'] ?? [] as $jt) {
            $jobTypeLabels[] = OnboardingData::JOB_TYPES[$jt] ?? $jt;
        }
        $jobTypes = implode(', ', $jobTypeLabels);

        $locationLabels = [];
        foreach ($profile['preferred_locations'] ?? [] as $loc) {
            $locationLabels[] = OnboardingData::LOCATIONS[$loc] ?? $loc;
        }
        $locations = implode(', ', $locationLabels);

        $age = $profile['age'] ?? null;

        return <<<PROMPT
You are matching a deaf/hard-of-hearing candidate to job vacancies in Indonesia's Jabodetabek area.

=== CANDIDATE PROFILE ===
Disability: {$disability}
Hearing: {$hearingLabel}
Communication: {$communication}
Work environment: {$workEnv}
Skills: {$skillCats} — {$subSkills}
Education: {$education}
Preferred job types: {$jobTypes}
Preferred locations: {$locations}
Age: {$age}

=== SCORING (0-100) ===
- Disability fit 35%: Can a deaf person effectively do this job given its communication demands? Jobs requiring heavy verbal phone/meeting communication without accommodation = LOW score. Text-based/visual jobs = HIGH score.
- Skill match 30%: How well do the candidate's skill categories and sub-skills align with the job's required skills?
- Environment fit 20%: How well does the work type, location, and accommodation match the candidate's preferences?
- Communication 10%: How well do the job's communication methods match the candidate's communication methods?
- Education 5%: How well does the candidate's education level and major match the job requirements?

=== SCORING GUIDE ===
0-20: No compatibility  21-40: Poor  41-60: Fair  61-80: Good match  81-100: Excellent match

=== RESPONSE FORMAT ===
Return ONLY JSON:
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
      "match_reason": "<1 sentence summary in Bahasa Indonesia>"
    }
  ]
}

=== JOB LISTINGS ===
{$jobList}
PROMPT;
    }
}
