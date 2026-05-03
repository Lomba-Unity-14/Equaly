<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Lowongan')]
class Lowongan extends Component
{
    public function render()
    {
        return view('livewire.lowongan');
    }
}
