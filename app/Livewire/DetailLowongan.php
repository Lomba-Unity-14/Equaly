<?php

namespace App\Livewire;

use App\Models\CompanyReview;
use App\Models\JobApplication;
use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
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

        $url = $this->job->url;
        $this->js('window.open('.json_encode($url).', "_blank")');
    }

    public function render()
    {
        return view('livewire.detail-lowongan');
    }
}
