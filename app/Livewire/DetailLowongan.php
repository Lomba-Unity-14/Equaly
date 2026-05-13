<?php

namespace App\Livewire;

use App\Models\CompanyReview;
use App\Models\JobApplication;
use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
use App\Models\TrainingPartner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.detail')]
#[Title('Equaly - Detail Lowongan')]
class DetailLowongan extends Component
{
    public ?JobVacancyData $job = null;

    public ?JobUserMatch $match = null;

    public array $companyReviews = [];

    public array $companyScore = [];

    public function mount(string $lowongan): void
    {
        $this->job = JobVacancyData::find($lowongan);

        if (! $this->job) {
            abort(404);
        }

        if (auth()->check()) {
            $this->match = JobUserMatch::where('job_vacancy_data_id', $this->job->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        if ($this->job->company) {
            $reviews = CompanyReview::with('user')
                ->where('company_name', $this->job->company)
                ->latest()
                ->get();

            $this->companyReviews = $reviews->toArray();

            $friendlyCount = $reviews->where('is_friendly', true)->count();
            $total = $reviews->count();
            $this->companyScore = [
                'total' => $total,
                'friendly' => $friendlyCount,
                'score' => $total > 0 ? round(($friendlyCount / $total) * 100) : 0,
            ];
        }
    }

    public function apply(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        JobApplication::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'job_vacancy_data_id' => $this->job->id,
            ],
            [
                'company_name' => $this->job->company,
                'applied_at' => now(),
            ]
        );

        $url = $this->job->job_url;
        $this->js('window.open('.json_encode($url).', "_blank")');
    }

    public function render()
    {
        $academyRecommendation = $this->getAcademyRecommendation();

        return view('livewire.detail-lowongan', [
            'academyRecommendation' => $academyRecommendation,
        ]);
    }

    protected function getAcademyRecommendation(): ?TrainingPartner
    {
        if (! $this->match || ! auth()->check()) {
            return null;
        }

        $score = $this->match->match_score;
        if ($score < 40 || $score >= 80) {
            return null;
        }

        $profile = auth()->user()->profile;
        if (! $profile) {
            return null;
        }

        $skillCategories = $profile->skill_categories ?? [];
        if ($skillCategories && isset($skillCategories[0]) && is_array($skillCategories[0])) {
            $userCategories = array_column($skillCategories, 'category');
        } else {
            $userCategories = $skillCategories;
        }

        if (empty($userCategories)) {
            return null;
        }

        return TrainingPartner::where('is_active', true)
            ->whereIn('type', ['pelatihan', 'sertifikasi'])
            ->where(function ($q) use ($userCategories) {
                $q->whereIn('category', $userCategories)
                    ->orWhere('category', 'general');
            })
            ->orderBy('name')
            ->first();
    }
}
