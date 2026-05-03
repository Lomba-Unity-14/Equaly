<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Equaly - Beranda')]
class Beranda extends Component
{
    public function render()
    {
        return view('livewire.beranda');
    }
}
