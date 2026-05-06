<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.onboarding')]
class Onboarding extends Component
{
    public int $step = 1;

    public array $disability_condition = [];

    public array $communication_preference = [];

    public array $work_environment = [];

    public array $skills = [];

    public string $newSkill = '';

    public function mount(): void
    {
        $existing = auth()->user()->profile;
        if ($existing) {
            $this->disability_condition = is_string($existing->disability_condition) ? [$existing->disability_condition] : ($existing->disability_condition ?? []);
            $this->communication_preference = is_string($existing->communication_preference) ? [$existing->communication_preference] : ($existing->communication_preference ?? []);
            $this->work_environment = is_string($existing->work_environment) ? [$existing->work_environment] : ($existing->work_environment ?? []);
            $this->skills = $existing->skills ?? [];
        }
    }

    public function next(): void
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function toggleCondition(string $property, string $value): void
    {
        if (in_array($value, $this->$property)) {
            $this->$property = array_values(array_diff($this->$property, [$value]));
        } else {
            $this->$property[] = $value;
        }
    }

    public function addSkill(): void
    {
        $skill = trim($this->newSkill);
        if ($skill !== '' && ! in_array($skill, $this->skills)) {
            $this->skills[] = $skill;
        }
        $this->newSkill = '';
    }

    public function removeSkill(int $index): void
    {
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    public function save(): void
    {
        $this->validateCurrentStep();

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'disability_condition' => $this->disability_condition,
                'communication_preference' => $this->communication_preference,
                'work_environment' => $this->work_environment,
                'skills' => $this->skills,
                'onboarding_completed' => true,
            ]
        );

        \Illuminate\Support\Facades\Cache::put('matching_status_' . auth()->id(), 'processing', now()->addMinutes(10));
        \App\Jobs\MatchUserToJobs::dispatch(auth()->id());

        session()->flash('success', 'Profil berhasil dilengkapi!');
        $this->redirect(route('matching'), navigate: true);
    }

    protected function validateCurrentStep(): void
    {
        $rules = match ($this->step) {
            1 => ['disability_condition' => 'required|array|min:1'],
            2 => ['communication_preference' => 'required|array|min:1'],
            3 => ['work_environment' => 'required|array|min:1'],
            4 => ['skills' => 'required|array|min:1'],
            default => [],
        };

        if ($rules !== []) {
            $this->validate($rules);
        }
    }

    public function render()
    {
        return view('livewire.onboarding');
    }
}
