<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.detail')]
#[Title('Equaly - Detail Lowongan')]
class DetailLowongan extends Component
{
    public string $lowongan;

    public function mount(string $lowongan): void
    {
        $this->lowongan = $lowongan;
    }

    public function render()
    {
        return view('livewire.detail-lowongan');
    }
}
