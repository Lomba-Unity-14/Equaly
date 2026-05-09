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
                        'content' => 'You are an expert job matching system for deaf and hard-of-hearing individuals in Indonesia. You assess how well job vacancies match a deaf candidate profile, paying special attention to communication compatibility, disability accommodation, and skill alignment. Always respond with valid JSON only.',
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
            $jobList .= "- Company: {$job['company']}\n";
            $jobList .= "- Category: {$job['job_category']}\n";
            $jobList .= "- Skills required: {$job['skill_req']}\n";
            $jobList .= "- Education: {$job['education_req']}\n";
            $jobList .= "- Work type: {$job['work_type']}\n";
            $jobList .= "- Location: {$job['location']}\n";
            $jobList .= '- Description: '.mb_substr($job['jobdesk'], 0, 800)."\n\n";
        }

        $disability = implode(', ', $profile['disability_condition'] ?? []);
        $hearingLabel = OnboardingData::HEARING_LEVELS[$profile['hearing_level'] ?? ''] ?? ($profile['hearing_level'] ?? 'Tidak diketahui');
        $hearingDesc = OnboardingData::HEARING_DESCRIPTIONS[$profile['hearing_level'] ?? ''] ?? '';

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

        $educationLabel = OnboardingData::EDUCATION_LEVELS[$profile['education_level'] ?? ''] ?? ($profile['education_level'] ?? 'Tidak diketahui');
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
You are matching a DEAF/HARD-OF-HEARING candidate to job vacancies in Indonesia's Jabodetabek area. Evaluate each job carefully.

=== CANDIDATE PROFILE ===
Disability: {$disability}
Hearing level: {$hearingLabel} — {$hearingDesc}
Communication preference: {$communication}
Preferred work environment & accommodation: {$workEnv}
Skill categories: {$skillCats}
Specific sub-skills: {$subSkills}
Education: {$education}
Preferred job types: {$jobTypes}
Preferred locations: {$locations}
Age: {$age} years old

=== WEIGHTING ===
Match scores (0-100) should use these weights:
- Disability fit (35%): Can this deaf/hard-of-hearing person effectively perform this job? Consider: Does the job require extensive verbal phone communication? Does it require constant in-person verbal meetings without accommodation? Or is it primarily text-based/visual? A job that requires heavy verbal communication should score LOW for deaf candidates. A job that can be done via text/visual means should score HIGH.
- Skill match (30%): Do the candidate's skill categories and sub-skills align with the job's required skills and category?
- Work environment (20%): Does the work type and location match the candidate's preferences? Consider: Remote vs on-site, Jabodetabek location match, job type (full-time/part-time/contract), and accommodation needs (sign language interpreter, visual notifications).
- Communication (10%): Does the job's communication demands match the candidate's specific communication methods? For example: a candidate using BISINDO (sign language) would match well with companies that provide sign language interpreters. A candidate preferring text-based communication would match well with remote/async jobs. A candidate who can lip-read may still manage some in-person roles.
- Education (5%): Does the candidate's education level and major match the job requirements?

=== SCORING GUIDE ===
0-20: Not compatible at all — job requires extensive verbal communication without accommodation
21-40: Poor match — significant communication or skill gaps
41-60: Somewhat compatible — possible with accommodations
61-80: Good match — candidate can perform well with existing skills and communication methods
81-100: Excellent match — strong alignment across all dimensions

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
      "match_reason": "<1-2 sentence summary in Bahasa Indonesia explaining WHY this job is suitable or not suitable for this deaf candidate. Mention specific communication considerations.>"
    }
  ]
}

=== JOB LISTINGS ===
{$jobList}
PROMPT;
    }
}
