<div class="flex flex-col items-center justify-center min-h-screen px-6 text-center" wire:poll.2s="checkStatus">
    @if($status === 'processing')
        <div class="mb-8 relative">
            <div class="w-24 h-24 rounded-full bg-primary-fixed-dim flex items-center justify-center animate-pulse shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-[48px] text-primary" style="font-variation-settings: 'FILL' 1;">psychology_alt</span>
            </div>
            <div class="absolute inset-0 w-24 h-24 rounded-full border-2 border-primary animate-spin" style="animation-duration: 3s; border-top-color: transparent; border-left-color: transparent;"></div>
            <div class="absolute -inset-2 w-32 h-32 rounded-full border border-primary/20 animate-spin" style="animation-duration: 5s; animation-direction: reverse; border-top-color: transparent; border-bottom-color: transparent;"></div>
        </div>

        <h1 class="font-h1 text-h1 text-text-primary mb-2">AI Sedang Bekerja</h1>
        <h2 class="font-h2 text-h2 text-text-primary mb-3">Mencocokkan pekerjaan untukmu<span class="dots"></span></h2>
        <p class="font-body-sm text-body-sm text-text-secondary max-w-xs">
            AI kami sedang menganalisis profilmu dan mencocokkannya dengan lowongan yang tersedia. Proses ini hanya perlu beberapa saat.
        </p>

        <div class="mt-8 flex items-center gap-2 text-text-secondary font-label-caps text-label-caps">
            <span class="w-2 h-2 rounded-full bg-primary animate-bounce" style="animation-delay: 0s;"></span>
            <span class="w-2 h-2 rounded-full bg-primary animate-bounce" style="animation-delay: 0.2s;"></span>
            <span class="w-2 h-2 rounded-full bg-primary animate-bounce" style="animation-delay: 0.4s;"></span>
        </div>

    @elseif($status === 'failed')
        <div class="mb-8">
            <div class="w-24 h-24 rounded-full bg-error-container flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-[48px] text-on-error-container" style="font-variation-settings: 'FILL' 1;">error</span>
            </div>
        </div>

        <h1 class="font-h1 text-h1 text-text-primary mb-2">Terjadi Kendala</h1>
        <p class="font-body-sm text-body-sm text-text-secondary max-w-xs mb-8">
            AI tidak dapat menyelesaikan pencocokan saat ini. Jangan khawatir, kamu bisa mencoba lagi.
        </p>

        <button wire:click="retry"
            class="bg-primary text-on-primary font-label-caps text-label-caps py-3 px-8 rounded-full hover:bg-primary-fixed-variant transition-all min-h-[48px] inline-flex items-center justify-center shadow-lg active:scale-95">
            <span class="material-symbols-outlined text-[20px] mr-2">refresh</span>
            Coba Lagi
        </button>
    @endif
</div>

<style>
    .dots::after {
        content: '';
        animation: dotPulse 1.5s steps(4, end) infinite;
    }

    @keyframes dotPulse {
        0% { content: ''; }
        25% { content: '.'; }
        50% { content: '..'; }
        75% { content: '...'; }
        100% { content: ''; }
    }
</style>
