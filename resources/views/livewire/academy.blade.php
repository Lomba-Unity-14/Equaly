<div class="flex flex-col gap-stack-lg">
    {{-- Hero Banner --}}
    <section>
        <div class="relative h-44 rounded-2xl overflow-hidden bg-linear-to-br from-primary to-primary-container shadow-sm flex items-center">
            <div class="absolute -right-8 -top-8 opacity-[0.08] pointer-events-none">
                <span class="material-symbols-outlined text-[120px] text-on-primary" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <div class="relative z-10 p-6">
                <span class="material-symbols-outlined text-4xl text-on-primary mb-3 block">school</span>
                <h1 class="font-h1 text-h1 font-extrabold leading-tight text-on-primary">Tingkatkan Skill,<br/>Buka Peluang</h1>
                <p class="text-body-sm mt-2 text-on-primary/80">Kembangkan potensi bersama mitra pelatihan terbaik.</p>
            </div>
        </div>
    </section>

    {{-- Rekomendasi Untukmu --}}
    @if($recommended->isNotEmpty())
    <section class="flex flex-col gap-stack-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-primary-fixed-dim flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <h2 class="font-h2 text-h2 text-text-primary">Rekomendasi Untukmu</h2>
        </div>
        <div class="flex flex-col gap-3">
            @foreach($recommended as $partner)
                @include('livewire.partials.partner-card', ['partner' => $partner])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Program Pemerintah --}}
    @if($government->isNotEmpty())
    <section class="flex flex-col gap-stack-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-secondary-container flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">account_balance</span>
            </div>
            <h2 class="font-h2 text-h2 text-text-primary">Program Pemerintah</h2>
        </div>
        <div class="flex flex-col gap-3">
            @foreach($government as $partner)
                @include('livewire.partials.partner-card', ['partner' => $partner])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Komunitas --}}
    @if($community->isNotEmpty())
    <section class="flex flex-col gap-stack-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">diversity_3</span>
            </div>
            <h2 class="font-h2 text-h2 text-text-primary">Komunitas</h2>
        </div>
        <div class="flex flex-col gap-3">
            @foreach($community as $partner)
                @include('livewire.partials.partner-card', ['partner' => $partner])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Educational Partners --}}
    <section class="pb-6 pt-2">
        <h3 class="font-label-caps text-text-secondary mb-6 tracking-widest text-center">MITRA EDUKASI KREDIBEL</h3>
        <div class="flex flex-wrap justify-center items-center gap-8 grayscale opacity-60">
            <div class="flex flex-col items-center gap-1">
                <span class="material-symbols-outlined text-3xl">school</span>
                <span class="font-bold text-xs">UNIV-A</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="material-symbols-outlined text-3xl">corporate_fare</span>
                <span class="font-bold text-xs">TECHCORP</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="material-symbols-outlined text-3xl">handshake</span>
                <span class="font-bold text-xs">GOV-SKILL</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="material-symbols-outlined text-3xl">verified</span>
                <span class="font-bold text-xs">CERT-PLUS</span>
            </div>
        </div>
    </section>

    {{-- Empty state --}}
    @if($recommended->isEmpty() && $government->isEmpty() && $community->isEmpty())
    <div class="text-center py-12">
        <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-[32px] text-outline">school</span>
        </div>
        <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada rekomendasi</p>
        <p class="font-body-sm text-body-sm text-outline">Lengkapi profilmu agar rekomendasi pelatihan muncul di sini.</p>
    </div>
    @endif
</div>
