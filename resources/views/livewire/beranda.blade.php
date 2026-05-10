<div class ="flex flex-col gap-stack-lg">
    <div>
        <h1 class="font-h1 text-h1 text-text-primary mb-1">Halo, {{ $user->name }}</h1>
        <p class="font-body-sm text-body-sm text-text-secondary">Siap mencari peluang baru hari ini?</p>
    </div>

    <div class="relative drop-shadow-md">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline">search</span>
        </div>
        <input
            class="block w-full pl-12 pr-4 py-3 bg-surface border-2 border-border-subtle rounded-2xl text-body-lg font-body-lg text-text-primary placeholder-outline focus:ring-primary focus:border-primary min-h-[48px] shadow-sm transition-all focus:shadow-md focus:outline-none"
            placeholder="Cari pekerjaan inklusif..." type="text" />
    </div>

    <div
        class="bg-linear-to-br from-secondary-container to-primary-fixed-dim rounded-3xl p-6 relative overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-white/50 transform">
        <div class="relative z-10 w-2/3">
            <h2 class="font-h2 text-h2 text-on-secondary-container mb-4 font-bold">Tingkatkan skill UI/UX kamu ke 70%!
            </h2>
            <button
                class="bg-primary text-on-primary font-label-caps text-label-caps py-3 px-6 rounded-full hover:bg-primary-fixed-variant transition-all min-h-[48px] inline-flex items-center justify-center shadow-lg hover:shadow-xl active:scale-95">
                Mulai Belajar
            </button>
        </div>
        <!-- Decorative Element -->
        <div class="absolute right-10 bottom-5 opacity-20 pointer-events-none transform rotate-[-10deg] scale-600">
            <span class="material-symbols-outlined text-[140px] text-primary"
                style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
    </div>

    @if($matchingStatus === 'processing')
        <section class="flex flex-col gap-stack-md" wire:poll.2s="$refresh">
            <h2 class="font-h2 text-h2 text-text-primary">Rekomendasi Untukmu</h2>

            <div class="flex items-center gap-2 text-text-secondary font-body-sm text-body-sm mb-2">
                <span class="material-symbols-outlined text-[18px] animate-spin">refresh</span>
                <span>AI sedang menganalisa profilmu...</span>
            </div>

            @for($i = 0; $i < 3; $i++)
                <div class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 animate-pulse">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-surface-container-high shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-5 bg-surface-container-high rounded w-3/4"></div>
                            <div class="h-4 bg-surface-container-high rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div class="h-6 bg-surface-container-high rounded-full w-20"></div>
                        <div class="h-6 bg-surface-container-high rounded-full w-16"></div>
                    </div>
                </div>
            @endfor
        </section>

    @elseif($matchingStatus === 'failed')
        <section class="flex flex-col gap-stack-md" wire:poll.5s="$refresh">
            <h2 class="font-h2 text-h2 text-text-primary">Rekomendasi Untukmu</h2>

            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-error-container flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px] text-on-error-container">error</span>
                </div>
                <p class="font-body-lg text-body-lg text-text-secondary mb-1">AI mengalami kendala</p>
                <p class="font-body-sm text-body-sm text-outline mb-4">Tidak dapat menyelesaikan pencocokan. Coba lagi nanti.</p>
                <button wire:click="retry"
                    class="bg-primary text-on-primary font-label-caps text-label-caps py-2 px-6 rounded-full hover:bg-primary-fixed-variant transition-all min-h-[44px] inline-flex items-center justify-center shadow-lg active:scale-95">
                    <span class="material-symbols-outlined text-[18px] mr-2">refresh</span>
                    Coba Lagi
                </button>
            </div>
        </section>

    @else
        <section class="flex flex-col gap-stack-md">
            <h2 class="font-h2 text-h2 text-text-primary">Rekomendasi Untukmu</h2>

            @forelse($matches as $match)
                @include('livewire.partials.job-card', ['match' => $match, 'companyAggregates' => $companyAggregates])
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px] text-outline">search_off</span>
                    </div>
                    <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada rekomendasi</p>
                    <p class="font-body-sm text-body-sm text-outline">Coba lengkapi profilmu agar lowongan yang cocok muncul di sini.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
