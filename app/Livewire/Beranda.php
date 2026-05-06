<?php

namespace App\Livewire;

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

        $user = auth()->user();

        return view('livewire.beranda', [
            'matches' => $matches,
            'user' => $user,
        ]);
    }
}
