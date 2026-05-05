<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function save(): void
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Kata sandi saat ini salah.');

            return;
        }

        $user->update([
            'password' => $this->password,
        ]);

        $this->dispatch('password-changed');
        $this->close();
    }

    public function close(): void
    {
        $this->dispatch('closeChangePassword');
    }

    public function render()
    {
        return view('livewire.change-password');
    }
}
