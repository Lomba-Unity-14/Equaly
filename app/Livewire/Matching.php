<?php

namespace App\Livewire;

use App\Jobs\MatchUserToJobs;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.matching')]
#[Title('Equaly — Mencocokkan Pekerjaan')]
class Matching extends Component
{
    public string $status = 'processing';

    public int $progressStep = 0;

    protected const TIMEOUT_SECONDS = 90;

    protected ?int $startedAt = null;

    public function mount(): void
    {
        $this->startedAt = time();

        $cacheKey = 'matching_status_' . auth()->id();
        $cached = Cache::get($cacheKey);

        if ($cached === 'completed') {
            $this->redirect(route('beranda'), navigate: true);

            return;
        }

        if ($cached !== 'processing') {
            $this->dispatchJob();
        }
    }

    public function checkStatus(): void
    {
        $this->progressStep = ($this->progressStep + 1) % 5;

        $cacheKey = 'matching_status_' . auth()->id();
        $cached = Cache::get($cacheKey);

        if ($cached === 'completed') {
            $this->status = 'completed';
            $this->redirect(route('beranda'), navigate: true);

            return;
        }

        if ($cached === 'failed') {
            $this->status = 'failed';

            return;
        }

        if ($this->startedAt && (time() - $this->startedAt) > self::TIMEOUT_SECONDS) {
            $this->status = 'failed';
            Cache::put($cacheKey, 'failed', now()->addHours(1));

            return;
        }
    }

    public function retry(): void
    {
        $this->status = 'processing';
        $this->startedAt = time();
        $this->dispatchJob();
    }

    protected function dispatchJob(): void
    {
        Cache::put('matching_status_' . auth()->id(), 'processing', now()->addMinutes(10));
        MatchUserToJobs::dispatch(auth()->id());
    }

    public function render()
    {
        return view('livewire.matching');
    }
}
