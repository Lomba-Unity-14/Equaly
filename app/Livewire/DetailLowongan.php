<?php

namespace App\Livewire;

use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.detail')]
#[Title('Equaly - Detail Lowongan')]
class DetailLowongan extends Component
{
    public ?JobVacancyData $job = null;

    public ?JobUserMatch $match = null;

    public function mount(string $lowongan): void
    {
        $this->job = JobVacancyData::find($lowongan);

        if (! $this->job) {
            abort(404);
        }

        if (auth()->check()) {
            $this->match = JobUserMatch::where('job_vacancy_data_id', $this->job->id)
                ->where('user_id', auth()->id())
                ->first();
        }
    }

    public function render()
    {
        return view('livewire.detail-lowongan');
    }
}
