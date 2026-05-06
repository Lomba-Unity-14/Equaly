<?php

namespace App\Livewire;

use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Lowongan')]
class Lowongan extends Component
{
    public function render()
    {
        if (auth()->check()) {
            $matches = JobUserMatch::with('jobVacancyData')
                ->where('user_id', auth()->id())
                ->where('is_match', true)
                ->orderByDesc('match_score')
                ->get();
        } else {
            $jobs = JobVacancyData::all();
            $matches = collect();

            foreach ($jobs as $job) {
                $match = new JobUserMatch([
                    'job_vacancy_data_id' => $job->id,
                    'match_score' => 50,
                ]);
                $match->setRelation('jobVacancyData', $job);
                $matches->push($match);
            }
        }

        return view('livewire.lowongan', [
            'matches' => $matches,
        ]);
    }
}
