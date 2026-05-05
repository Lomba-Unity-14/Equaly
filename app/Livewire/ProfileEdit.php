<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileEdit extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $headline = '';

    public $avatar = null;

    public ?string $currentAvatar = null;

    public bool $removeAvatar = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->currentAvatar = $user->avatar;

        if ($user->profile) {
            $this->headline = $user->profile->headline ?? '';
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.auth()->id()],
            'headline' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $this->avatar->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        } elseif ($this->removeAvatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->update(['avatar' => null]);
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['headline' => $this->headline ?: null],
        );

        $this->dispatch('profile-edited');

        $this->close();
    }

    public function clearAvatar(): void
    {
        if ($this->avatar) {
            $this->avatar = null;
        } elseif ($this->currentAvatar) {
            $this->removeAvatar = true;
        }
    }

    public function undoClearAvatar(): void
    {
        $this->removeAvatar = false;
    }

    public function close(): void
    {
        $this->dispatch('closeEditProfile');
    }

    public function render()
    {
        return view('livewire.profile-edit');
    }
}
