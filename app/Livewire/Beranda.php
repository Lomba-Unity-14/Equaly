<?php

namespace App\Livewire;

use App\Models\CompanyReview;
use App\Models\JobUserMatch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Beranda')]
class Beranda extends Component
{
    public function render()
    {
        $matches = JobUserMatch::with('jobVacancyData')
            ->where('user_id', auth()->id())
            ->where('is_match', true)
            ->orderByDesc('match_score')
            ->limit(6)
            ->get();

        $companyNames = $matches->pluck('jobVacancyData.company')->filter()->unique();

        $companyAggregates = CompanyReview::whereIn('company_name', $companyNames)
            ->selectRaw("company_name, count(*) as total, sum(case when is_friendly = 1 then 1 else 0 end) as friendly")
            ->groupBy('company_name')
            ->get()
            ->keyBy('company_name');

        $user = auth()->user();

        return view('livewire.beranda', [
            'matches' => $matches,
            'user' => $user,
            'companyAggregates' => $companyAggregates,
        ]);
    }
}
