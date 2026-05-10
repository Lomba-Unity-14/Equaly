<?php

namespace App\Livewire;

use App\Data\OnboardingData;
use App\Models\CompanyReview;
use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Lowongan')]
class Lowongan extends Component
{
    public string $matchingStatus = 'idle';

    public function mount(): void
    {
        $this->matchingStatus = Cache::get('matching_status_'.auth()->id(), 'idle');
    }

    public function render()
    {
        $cached = Cache::get('matching_status_'.auth()->id());
        if ($cached && $cached !== $this->matchingStatus) {
            $this->matchingStatus = $cached;
        }

        if (auth()->check() && $this->matchingStatus === 'completed') {
            $matches = JobUserMatch::with('jobVacancyData')
                ->where('user_id', auth()->id())
                ->where('is_match', true)
                ->orderByDesc('match_score')
                ->get();
        } else {
            $jobs = JobVacancyData::all();

            $filteredJobs = auth()->check()
                ? $this->filterByUserSkills($jobs)
                : $jobs;

            $matches = collect();
            foreach ($filteredJobs as $job) {
                $match = new JobUserMatch([
                    'job_vacancy_data_id' => $job->id,
                    'match_score' => 50,
                ]);
                $match->setRelation('jobVacancyData', $job);
                $matches->push($match);
            }
        }

        $companyNames = $matches->pluck('jobVacancyData.company')->filter()->unique();

        $companyAggregates = CompanyReview::whereIn('company_name', $companyNames)
            ->selectRaw('company_name, count(*) as total, sum(case when is_friendly = 1 then 1 else 0 end) as friendly')
            ->groupBy('company_name')
            ->get()
            ->keyBy('company_name');

        return view('livewire.lowongan', [
            'matches' => $matches,
            'companyAggregates' => $companyAggregates,
        ]);
    }

    protected function filterByUserSkills($jobs): array
    {
        $profile = auth()->user()->profile;
        if (! $profile) {
            return $jobs->toArray();
        }

        $keywords = $this->extractSkillKeywords($profile);

        if (empty($keywords)) {
            return $jobs->toArray();
        }

        return $jobs->filter(function ($job) use ($keywords) {
            if (empty($job->skill_req)) {
                return false;
            }

            $reqLower = strtolower($job->skill_req);
            foreach ($keywords as $keyword) {
                if (str_contains($reqLower, $keyword)) {
                    return true;
                }
            }

            return false;
        })->values()->toArray();
    }

    protected function extractSkillKeywords($profile): array
    {
        $keywords = [];

        $skillCategories = $profile->skill_categories ?? [];
        if ($skillCategories && isset($skillCategories[0]) && is_array($skillCategories[0])) {
            $cats = array_column($skillCategories, 'category');
            $subs = [];
            foreach ($skillCategories as $entry) {
                foreach ($entry['subs'] ?? [] as $sub) {
                    $subs[] = $sub;
                }
            }
        } else {
            $cats = $skillCategories;
            $subs = [];
        }

        foreach ($cats as $cat) {
            $label = OnboardingData::SKILL_CATEGORIES[$cat] ?? $cat;
            foreach (explode(' ', strtolower($label)) as $word) {
                if (strlen($word) >= 3) {
                    $keywords[] = $word;
                }
            }
        }

        foreach ($subs as $sub) {
            foreach (OnboardingData::SKILL_SUBS as $cat => $subs) {
                $label = $subs[$sub] ?? null;
                if ($label) {
                    foreach (explode(' ', strtolower($label)) as $word) {
                        if (strlen($word) >= 3) {
                            $keywords[] = $word;
                        }
                    }
                    break;
                }
            }
        }

        return array_unique($keywords);
    }
}
