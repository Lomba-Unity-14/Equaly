<?php

namespace App\Livewire;

use App\Models\CompanyReview;
use App\Models\JobApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Histori Lamaran')]
class HistoriLamaran extends Component
{
    public ?int $reviewing = null;

    public int $step = 1;

    public bool $is_accepted = false;

    public int $has_disability_employees = 0;

    public bool $is_friendly = false;

    public string $experience = '';

    public function startReview(int $id): void
    {
        $application = JobApplication::where('user_id', auth()->id())->find($id);

        if (! $application || $application->review()->exists()) {
            return;
        }

        $this->reviewing = $id;
        $this->step = 1;
        $this->is_accepted = false;
        $this->has_disability_employees = 0;
        $this->is_friendly = false;
        $this->experience = '';
    }

    public function next(): void
    {
        $this->step++;
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function cancelReview(): void
    {
        $this->reviewing = null;
    }

    public function saveReview(): void
    {
        $application = JobApplication::where('user_id', auth()->id())->find($this->reviewing);

        if (! $application) {
            return;
        }

        CompanyReview::create([
            'user_id' => auth()->id(),
            'job_application_id' => $application->id,
            'company_name' => $application->company_name,
            'is_accepted' => $this->is_accepted,
            'has_disability_employees' => $this->has_disability_employees,
            'is_friendly' => $this->is_friendly,
            'experience' => $this->experience,
        ]);

        $this->reviewing = null;
    }

    public function deny(int $id): void
    {
        JobApplication::where('user_id', auth()->id())->where('id', $id)->delete();
    }

    public function render()
    {
        $applications = JobApplication::with(['jobVacancyData', 'review'])
            ->where('user_id', auth()->id())
            ->orderByDesc('applied_at')
            ->get();

        $reviewingApp = null;
        if ($this->reviewing) {
            $reviewingApp = JobApplication::with('jobVacancyData')
                ->where('user_id', auth()->id())
                ->find($this->reviewing);
        }

        return view('livewire.histori-lamaran', [
            'applications' => $applications,
            'reviewingApp' => $reviewingApp,
        ]);
    }
}
