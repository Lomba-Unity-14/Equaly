<?php

namespace App\Livewire;

use App\Data\OnboardingData;
use App\Jobs\MatchUserToJobs;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.onboarding')]
class Onboarding extends Component
{
    public int $step = 1;

    public const TOTAL_STEPS = 8;

    public string $disability_condition = 'tunarungu';

    public string $hearing_level = '';

    public array $communication_preference = [];

    public array $work_environment = [];

    public array $skill_categories = [];

    public array $sub_skills = [];

    public ?string $date_of_birth = null;

    public string $education_level = '';

    public string $education_major = '';

    public array $job_types = [];

    public array $preferred_locations = [];

    public bool $no_work_experience = false;

    public array $work_experiences = [];

    public function mount(): void
    {
        $existing = auth()->user()->profile;
        if ($existing) {
            $this->disability_condition = $existing->disability_condition[0] ?? 'tunarungu';
            $this->hearing_level = $existing->hearing_level ?? '';
            $this->communication_preference = $existing->communication_preference ?? [];
            $this->work_environment = $existing->work_environment ?? [];
            $this->education_level = $existing->education_level ?? '';
            $this->education_major = $existing->education_major ?? '';
            $this->job_types = $existing->job_types ?? [];
            $this->preferred_locations = $existing->preferred_locations ?? [];
            $this->date_of_birth = auth()->user()->date_of_birth?->format('Y-m-d');

            $existingExperiences = auth()->user()->workExperiences;
            if ($existingExperiences->isNotEmpty()) {
                $this->work_experiences = $existingExperiences->map(fn ($e) => [
                    'company_name' => $e->company_name,
                    'position' => $e->position,
                    'start_month' => $e->start_date ? $e->start_date->format('m') : '',
                    'start_year' => $e->start_date ? $e->start_date->format('Y') : '',
                    'still_working' => $e->end_date === null,
                    'end_month' => $e->end_date ? $e->end_date->format('m') : '',
                    'end_year' => $e->end_date ? $e->end_date->format('Y') : '',
                ])->toArray();
            } else {
                $this->no_work_experience = true;
            }

            $existingSkills = $existing->skill_categories ?? [];
            if ($existingSkills) {
                if (isset($existingSkills[0]) && is_array($existingSkills[0])) {
                    foreach ($existingSkills as $entry) {
                        $this->skill_categories[] = $entry['category'] ?? '';
                        foreach ($entry['subs'] ?? [] as $sub) {
                            $this->sub_skills[] = $sub;
                        }
                    }
                } else {
                    $this->skill_categories = $existingSkills;
                }
            }
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

    public function toggleArray(string $property, string $value): void
    {
        if (in_array($value, $this->$property)) {
            $this->$property = array_values(array_diff($this->$property, [$value]));
        } else {
            $this->$property[] = $value;
        }
    }

    public function toggleSkillCategory(string $category): void
    {
        if (in_array($category, $this->skill_categories)) {
            $this->skill_categories = array_values(array_diff($this->skill_categories, [$category]));
            $categorySubs = array_keys(OnboardingData::SKILL_SUBS[$category] ?? []);
            $this->sub_skills = array_values(array_diff($this->sub_skills, $categorySubs));
        } else {
            $this->skill_categories[] = $category;
        }
    }

    public function addWorkExperience(): void
    {
        $this->work_experiences[] = [
            'company_name' => '',
            'position' => '',
            'start_month' => '',
            'start_year' => '',
            'still_working' => false,
            'end_month' => '',
            'end_year' => '',
        ];
    }

    public function removeWorkExperience(int $index): void
    {
        unset($this->work_experiences[$index]);
        $this->work_experiences = array_values($this->work_experiences);
    }

    public function getYearRangeProperty(): array
    {
        $years = [];
        for ($y = (int) date('Y'); $y >= 1980; $y--) {
            $years[$y] = (string) $y;
        }

        return $years;
    }

    public function getMonthRangeProperty(): array
    {
        return OnboardingData::MONTHS;
    }

    public function save(): void
    {
        $this->validateCurrentStep();

        $skillCategoriesData = [];
        foreach ($this->skill_categories as $category) {
            $categorySubs = array_intersect($this->sub_skills, array_keys(OnboardingData::SKILL_SUBS[$category] ?? []));
            $skillCategoriesData[] = [
                'category' => $category,
                'subs' => array_values($categorySubs),
            ];
        }

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'disability_condition' => [$this->disability_condition],
                'hearing_level' => $this->hearing_level,
                'communication_preference' => $this->communication_preference,
                'work_environment' => $this->work_environment,
                'skill_categories' => $skillCategoriesData,
                'education_level' => $this->education_level,
                'education_major' => $this->education_major ?: null,
                'job_types' => $this->job_types,
                'preferred_locations' => $this->preferred_locations,
                'onboarding_completed' => true,
            ]
        );

        if ($this->date_of_birth) {
            auth()->user()->update(['date_of_birth' => $this->date_of_birth]);
        }

        auth()->user()->workExperiences()->delete();
        if (! $this->no_work_experience && ! empty($this->work_experiences)) {
            foreach ($this->work_experiences as $exp) {
                $startDate = ($exp['start_year'] && $exp['start_month'])
                    ? "{$exp['start_year']}-{$exp['start_month']}-01"
                    : null;
                $endDate = null;
                if (! ($exp['still_working'] ?? false) && $exp['end_year'] && $exp['end_month']) {
                    $endDate = "{$exp['end_year']}-{$exp['end_month']}-01";
                }
                auth()->user()->workExperiences()->create([
                    'company_name' => $exp['company_name'],
                    'position' => $exp['position'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }

        Cache::put('matching_status_'.auth()->id(), 'processing', now()->addMinutes(10));
        (new MatchUserToJobs(auth()->id()))->handle();

        session()->flash('success', 'Profil berhasil disimpan! AI sedang melakukan analisis, kami perlu waktu untuk mencocokkan profil kamu, mohon menunggu ya...');
        $this->redirect(route('beranda'), navigate: true);
    }

    public function getAvailableMajorsProperty(): array
    {
        if (empty($this->skill_categories)) {
            return [];
        }

        $isSmk = $this->education_level === 'sma_smk';

        return OnboardingData::getMajorsForCategories($this->skill_categories, $isSmk);
    }

    public function getShowMajorFieldProperty(): bool
    {
        return in_array($this->education_level, OnboardingData::EDUCATION_HAS_MAJOR)
            || $this->education_level === 'sma_smk';
    }

    public function getSubSkillsForCategoryProperty(): array
    {
        $result = [];
        foreach ($this->skill_categories as $category) {
            $result[$category] = OnboardingData::SKILL_SUBS[$category] ?? [];
        }

        return $result;
    }

    protected function validateCurrentStep(): void
    {
        $rules = match ($this->step) {
            1 => ['disability_condition' => 'required|string|in:tunarungu'],
            2 => ['hearing_level' => 'required|string|in:tuli_total,gangguan_berat,gangguan_ringan'],
            3 => ['communication_preference' => 'required|array|min:1'],
            4 => ['work_environment' => 'required|array|min:1'],
            5 => [
                'skill_categories' => 'required|array|min:1',
                'sub_skills' => 'required|array|min:1',
            ],
            6 => [
                'date_of_birth' => 'required|date|before:today',
                'education_level' => 'required|string|in:sd,smp,sma_smk,d1_d4,s1,profesi,s2,s3',
                'education_major' => $this->showMajorField ? 'required|string' : 'nullable|string',
                'job_types' => 'required|array|min:1',
                'preferred_locations' => 'required|array|min:1',
            ],
            7 => $this->no_work_experience
                ? []
                : [
                    'work_experiences' => 'required|array|min:1|max:10',
                    'work_experiences.*.company_name' => 'required|string|max:255',
                    'work_experiences.*.position' => 'required|string|max:255',
                    'work_experiences.*.start_month' => 'required|string|in:01,02,03,04,05,06,07,08,09,10,11,12',
                    'work_experiences.*.start_year' => 'required|integer|min:1980|max:'.((int) date('Y') + 1),
                    'work_experiences.*.still_working' => 'boolean',
                    'work_experiences.*.end_month' => 'nullable|string|in:01,02,03,04,05,06,07,08,09,10,11,12',
                    'work_experiences.*.end_year' => 'nullable|integer|min:1980|max:'.((int) date('Y') + 1),
                ],
            8 => [],
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
