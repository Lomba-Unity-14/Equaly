<?php

namespace App\Livewire;

use App\Models\TrainingPartner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Academy')]
class Academy extends Component
{
    public function render()
    {
        $userCategories = $this->getUserCategories();

        $query = TrainingPartner::where('is_active', true);

        if (! empty($userCategories)) {
            $query->where(function ($q) use ($userCategories) {
                $q->whereIn('category', $userCategories)->orWhere('category', 'general');
            });
        }

        $partners = $query->orderBy('type')->orderBy('name')->get();

        return view('livewire.academy', [
            'recommended' => $partners->whereIn('type', ['pelatihan', 'sertifikasi']),
            'government' => $partners->where('type', 'pemerintah'),
            'community' => $partners->where('type', 'komunitas'),
        ]);
    }

    protected function getUserCategories(): array
    {
        $profile = auth()->user()?->profile;

        if (! $profile) {
            return [];
        }

        $skillCategories = $profile->skill_categories ?? [];

        if ($skillCategories && isset($skillCategories[0]) && is_array($skillCategories[0])) {
            return array_column($skillCategories, 'category');
        }

        return $skillCategories;
    }
}
