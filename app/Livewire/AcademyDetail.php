<?php

namespace App\Livewire;

use App\Models\TrainingPartner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.academy-detail')]
#[Title('Equaly - Detail Academy')]
class AcademyDetail extends Component
{
    public ?TrainingPartner $partner = null;

    public function mount(string $academy): void
    {
        $this->partner = TrainingPartner::findOrFail($academy);
    }

    public function render()
    {
        return view('livewire.academy-detail');
    }
}
