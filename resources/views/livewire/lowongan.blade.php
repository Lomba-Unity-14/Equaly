<div class="flex flex-col gap-stack-lg">
    <div>
        <h1 class="font-h1 text-h1 text-text-primary mb-1">Lowongan</h1>
        <p class="font-body-sm text-body-sm text-text-secondary">Temukan pekerjaan inklusif untukmu.</p>
    </div>

    <div class="relative drop-shadow-md">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline">search</span>
        </div>
        <input wire:model.live.debounce.300ms="searchQuery"
            class="block w-full pl-12 pr-4 py-3 bg-surface border-2 border-border-subtle rounded-2xl text-body-lg font-body-lg text-text-primary placeholder-outline focus:ring-primary focus:border-primary min-h-[48px] shadow-sm transition-all focus:shadow-md focus:outline-none"
            placeholder="Cari pekerjaan inklusif..." type="text" />
    </div>

    @if($searchQuery !== '')
        <div class="font-body-sm text-body-sm text-text-secondary -mt-2">
            <strong>{{ $searchCount }}</strong> lowongan ditemukan untuk "<strong>{{ $searchQuery }}</strong>"
        </div>
    @endif

    @if($matchingStatus === 'processing' || $matchingStatus === 'failed')
        <div wire:poll.5s="$refresh" class="flex flex-col gap-stack-md">
            <h2 class="font-h2 text-h2 text-text-primary">Semua Lowongan</h2>

            @if($matchingStatus === 'processing')
                <div class="bg-primary-container/20 rounded-xl p-3 border border-primary/20 flex items-start gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">psychology_alt</span>
                    <div>
                        <p class="font-body-sm text-body-sm text-on-primary-container">
                            <strong>AI sedang menganalisa profilmu.</strong>
                            Skor kecocokan akan muncul setelah selesai. Menampilkan {{ $matches->count() }} lowongan berdasarkan keahlianmu.
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-error-container/20 rounded-xl p-3 border border-error/20 flex items-start gap-2">
                    <span class="material-symbols-outlined text-error text-[18px] shrink-0 mt-0.5">error</span>
                    <p class="font-body-sm text-body-sm text-on-error-container">
                        AI mengalami kendala saat menganalisa. Tekan tombol coba lagi di Beranda untuk memulai ulang.
                    </p>
                </div>
            @endif

            @forelse($matches as $match)
                @include('livewire.partials.job-card', ['match' => $match, 'companyAggregates' => $companyAggregates])
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px] text-outline">work_off</span>
                    </div>
                    <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada lowongan</p>
                    <p class="font-body-sm text-body-sm text-outline">Tidak ditemukan lowongan yang sesuai dengan keahlianmu.</p>
                </div>
            @endforelse
        </div>
    @else
        <section class="flex flex-col gap-stack-md">
            <h2 class="font-h2 text-h2 text-text-primary">Semua Lowongan</h2>

            @forelse($matches as $match)
                @include('livewire.partials.job-card', ['match' => $match, 'companyAggregates' => $companyAggregates])
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[32px] text-outline">work_off</span>
                    </div>
                    <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada lowongan</p>
                    <p class="font-body-sm text-body-sm text-outline">Silakan cek kembali nanti.</p>
                </div>
            @endforelse
        </section>
    @endif
</div>
