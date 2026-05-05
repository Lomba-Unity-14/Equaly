<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Profil')]
class Profil extends Component
{
    public $user;

    public $profile;

    public ?string $editing = null;

    public array $skills = [];

    public array $disability_condition = [];

    public array $communication_preference = [];

    public array $work_environment = [];

    public string $newSkill = '';

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->profile = $this->user->profile;

        if ($this->profile) {
            $this->skills = $this->profile->skills ?? [];
            $this->disability_condition = $this->profile->disability_condition ?? [];
            $this->communication_preference = $this->profile->communication_preference ?? [];
            $this->work_environment = $this->profile->work_environment ?? [];
        }
    }

    public function editSection(string $section): void
    {
        $this->editing = $section;

        if ($this->profile) {
            $this->skills = $this->profile->skills ?? [];
            $this->disability_condition = $this->profile->disability_condition ?? [];
            $this->communication_preference = $this->profile->communication_preference ?? [];
            $this->work_environment = $this->profile->work_environment ?? [];
        }
    }

    public function cancelEdit(): void
    {
        $this->editing = null;
        $this->newSkill = '';

        if ($this->profile) {
            $this->skills = $this->profile->skills ?? [];
            $this->disability_condition = $this->profile->disability_condition ?? [];
            $this->communication_preference = $this->profile->communication_preference ?? [];
            $this->work_environment = $this->profile->work_environment ?? [];
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

    public function toggleCondition(string $property, string $value): void
    {
        if (in_array($value, $this->$property)) {
            $this->$property = array_values(array_diff($this->$property, [$value]));
        } else {
            $this->$property[] = $value;
        }
    }

    public function saveSection(string $section): void
    {
        $data = match ($section) {
            'skills' => ['skills' => $this->skills],
            'disability_condition' => ['disability_condition' => $this->disability_condition],
            'communication_preference' => ['communication_preference' => $this->communication_preference],
            'work_environment' => ['work_environment' => $this->work_environment],
            default => [],
        };

        if ($data !== []) {
            auth()->user()->profile()->updateOrCreate(
                ['user_id' => auth()->id()],
                [...$data, 'onboarding_completed' => true],
            );
        }

        $this->profile = auth()->user()->profile;
        $this->editing = null;
        $this->newSkill = '';
    }

    public function render()
    {
        return view('livewire.profil', [
            'labelMap' => $this->labels(),
        ]);
    }

    private function labels(): array
    {
        return [
            'disability_condition' => [
                'tunarungu' => 'Tunarungu',
                'tunadaksa' => 'Tunadaksa',
                'netra' => 'Netra/Low Vision',
                'lainnya' => 'Tidak ada/Lainnya',
            ],
            'communication_preference' => [
                'full_teks' => 'Full Teks',
                'bibir' => 'Bisa membaca gerak bibir',
                'bahasa_isyarat' => 'Butuh juru bahasa isyarat',
            ],
            'work_environment' => [
                'remote' => 'Remote',
                'hybrid' => 'Hybrid',
                'onsite' => 'On-site',
            ],
        ];
    }
}
