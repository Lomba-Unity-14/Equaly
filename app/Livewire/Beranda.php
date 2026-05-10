<?php

namespace App\Livewire;

use App\Jobs\MatchUserToJobs;
use App\Models\CompanyReview;
use App\Models\JobUserMatch;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Beranda')]
class Beranda extends Component
{
    public string $matchingStatus = 'idle';

    public function mount(): void
    {
        $this->matchingStatus = Cache::get('matching_status_'.auth()->id(), 'idle');
    }

    public function retry(): void
    {
        Cache::put('matching_status_'.auth()->id(), 'processing', now()->addMinutes(10));
        (new MatchUserToJobs(auth()->id()))->handle();
        $this->matchingStatus = 'processing';
    }

    public function render()
    {
        $cached = Cache::get('matching_status_'.auth()->id());
        if ($cached && $cached !== $this->matchingStatus) {
            $this->matchingStatus = $cached;
        }

        $matches = collect();
        if ($this->matchingStatus === 'completed' || $this->matchingStatus === 'idle') {
            $matches = JobUserMatch::with('jobVacancyData')
                ->where('user_id', auth()->id())
                ->where('is_match', true)
                ->orderByDesc('match_score')
                ->limit(6)
                ->get();
        }

        $companyNames = $matches->pluck('jobVacancyData.company')->filter()->unique();

        $companyAggregates = CompanyReview::whereIn('company_name', $companyNames)
            ->selectRaw('company_name, count(*) as total, sum(case when is_friendly = 1 then 1 else 0 end) as friendly')
            ->groupBy('company_name')
            ->get()
            ->keyBy('company_name');

        return view('livewire.beranda', [
            'matches' => $matches,
            'user' => auth()->user(),
            'companyAggregates' => $companyAggregates,
        ]);
    }
}
