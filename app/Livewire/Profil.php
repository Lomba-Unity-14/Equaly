<?php

namespace App\Livewire;

use App\Data\OnboardingData;
use App\Jobs\MatchUserToJobs;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Profil')]
class Profil extends Component
{
    public $user;

    public $profile;

    public bool $showEditProfile = false;

    public bool $showChangePassword = false;

    public ?string $editing = null;

    public string $hearing_level = '';

    public array $communication_preference = [];

    public array $work_environment = [];

    public array $skill_categories = [];

    public array $sub_skills = [];

    public string $education_level = '';

    public string $education_major = '';

    public array $job_types = [];

    public array $preferred_locations = [];

    public bool $needsRematch = false;

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->profile = $this->user->profile;

        if ($this->profile) {
            $this->hearing_level = $this->profile->hearing_level ?? '';
            $this->communication_preference = $this->profile->communication_preference ?? [];
            $this->work_environment = $this->profile->work_environment ?? [];
            $this->education_level = $this->profile->education_level ?? '';
            $this->education_major = $this->profile->education_major ?? '';
            $this->job_types = $this->profile->job_types ?? [];
            $this->preferred_locations = $this->profile->preferred_locations ?? [];

            $existingSkills = $this->profile->skill_categories ?? [];
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

    public function editSection(string $section): void
    {
        $this->editing = $section;
    }

    public function cancelEdit(): void
    {
        $this->editing = null;

        if ($this->profile) {
            $this->hearing_level = $this->profile->hearing_level ?? '';
            $this->communication_preference = $this->profile->communication_preference ?? [];
            $this->work_environment = $this->profile->work_environment ?? [];
            $this->education_level = $this->profile->education_level ?? '';
            $this->education_major = $this->profile->education_major ?? '';
            $this->job_types = $this->profile->job_types ?? [];
            $this->preferred_locations = $this->profile->preferred_locations ?? [];
            $this->skill_categories = [];
            $this->sub_skills = [];

            $existingSkills = $this->profile->skill_categories ?? [];
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

    public function saveSection(string $section): void
    {
        $data = match ($section) {
            'hearing_level' => ['hearing_level' => $this->hearing_level],
            'communication_preference' => ['communication_preference' => $this->communication_preference],
            'work_environment' => ['work_environment' => $this->work_environment],
            'education' => [
                'education_level' => $this->education_level,
                'education_major' => $this->education_major ?: null,
            ],
            'job_types' => ['job_types' => $this->job_types],
            'preferred_locations' => ['preferred_locations' => $this->preferred_locations],
            'skill_categories' => function () {
                $skillCategoriesData = [];
                foreach ($this->skill_categories as $category) {
                    $categorySubs = array_intersect($this->sub_skills, array_keys(OnboardingData::SKILL_SUBS[$category] ?? []));
                    $skillCategoriesData[] = [
                        'category' => $category,
                        'subs' => array_values($categorySubs),
                    ];
                }

                return ['skill_categories' => $skillCategoriesData];
            },
            default => [],
        };

        if (is_callable($data)) {
            $data = $data();
        }

        if ($data !== []) {
            auth()->user()->profile()->updateOrCreate(
                ['user_id' => auth()->id()],
                [...$data, 'onboarding_completed' => true],
            );

            $this->needsRematch = true;
        }

        $this->profile = auth()->user()->profile;
        $this->editing = null;
    }

    public function getSubSkillsForCategoryProperty(): array
    {
        $result = [];
        foreach ($this->skill_categories as $category) {
            $result[$category] = OnboardingData::SKILL_SUBS[$category] ?? [];
        }

        return $result;
    }

    public function getAvailableMajorsProperty(): array
    {
        if (empty($this->skill_categories)) {
            return [];
        }

        $isSmk = $this->education_level === 'sma_smk';

        return OnboardingData::getMajorsForCategories($this->skill_categories, $isSmk);
    }

    #[On('closeEditProfile')]
    public function closeEditProfile(): void
    {
        $this->showEditProfile = false;
    }

    #[On('closeChangePassword')]
    public function closeChangePassword(): void
    {
        $this->showChangePassword = false;
    }

    #[On('profile-edited')]
    public function refreshProfile(): void
    {
        $this->user = auth()->user();
        $this->profile = $this->user->profile;

        if ($this->profile) {
            $this->hearing_level = $this->profile->hearing_level ?? '';
            $this->communication_preference = $this->profile->communication_preference ?? [];
            $this->work_environment = $this->profile->work_environment ?? [];
            $this->education_level = $this->profile->education_level ?? '';
            $this->education_major = $this->profile->education_major ?? '';
            $this->job_types = $this->profile->job_types ?? [];
            $this->preferred_locations = $this->profile->preferred_locations ?? [];
            $this->skill_categories = [];
            $this->sub_skills = [];

            $existingSkills = $this->profile->skill_categories ?? [];
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

    #[On('password-changed')]
    public function passwordChanged(): void
    {
        session()->flash('success', 'Kata sandi berhasil diubah.');
    }

    public function rematch(): void
    {
        Cache::put('matching_status_'.auth()->id(), 'processing', now()->addMinutes(10));
        MatchUserToJobs::dispatch(auth()->id());
        $this->redirect(route('matching'), navigate: true);
    }

    public function render()
    {
        $latestApplication = JobApplication::with(['jobVacancyData', 'review'])
            ->where('user_id', auth()->id())
            ->orderByDesc('applied_at')
            ->first();

        $pendingCount = JobApplication::where('user_id', auth()->id())
            ->whereDoesntHave('review')
            ->count();

        return view('livewire.profil', [
            'latestApplication' => $latestApplication,
            'pendingCount' => $pendingCount,
        ]);
    }
}
