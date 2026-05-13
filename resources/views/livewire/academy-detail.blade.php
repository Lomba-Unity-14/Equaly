@php
    use App\Data\OnboardingData;

    $typeLabels = [
        'pelatihan' => ['label' => 'Pelatihan', 'icon' => 'school', 'variant' => 'medium'],
        'sertifikasi' => ['label' => 'Sertifikasi', 'icon' => 'verified', 'variant' => 'high'],
        'pemerintah' => ['label' => 'Pemerintah', 'icon' => 'account_balance', 'variant' => 'medium'],
        'komunitas' => ['label' => 'Komunitas', 'icon' => 'diversity_3', 'variant' => 'low'],
    ];
    $typeInfo = $typeLabels[$partner->type] ?? ['label' => $partner->type, 'icon' => 'school', 'variant' => 'default'];
    $categoryLabel = OnboardingData::SKILL_CATEGORIES[$partner->category] ?? ($partner->category === 'general' ? 'Umum' : $partner->category);

    $accLabels = [
        'bisindo' => ['label' => 'BISINDO (Bahasa Isyarat)', 'icon' => 'sign_language'],
        'sibi' => ['label' => 'SIBI (Sistem Isyarat)', 'icon' => 'sign_language'],
        'teks_tertulis' => ['label' => 'Teks Tertulis (Materi & Komunikasi)', 'icon' => 'chat'],
        'juru_isyarat' => ['label' => 'Juru Bahasa Isyarat (Pendamping)', 'icon' => 'accessibility'],
        'notifikasi_visual' => ['label' => 'Notifikasi Visual (Lampu/Layar)', 'icon' => 'notifications'],
    ];
@endphp

<div class="flex flex-col gap-stack-lg">
    {{-- Header --}}
    <section class="bg-surface rounded-2xl border border-outline-variant shadow-sm p-6 flex flex-col items-center text-center">
        <div class="w-16 h-16 rounded-2xl bg-primary-fixed-dim flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[36px] text-primary" style="font-variation-settings: 'FILL' 1;">{{ $typeInfo['icon'] }}</span>
        </div>
        <h1 class="font-h1 text-h1 text-text-primary mb-1">{{ $partner->name }}</h1>
        <div class="flex items-center gap-2 text-text-secondary font-body-sm text-body-sm mb-3">
            <span>{{ $categoryLabel }}</span>
            <span class="w-1 h-1 rounded-full bg-outline"></span>
            <x-badge :icon="$typeInfo['icon']" :text="$typeInfo['label']" :variant="$typeInfo['variant']" />
        </div>
        <div class="flex flex-wrap gap-2 justify-center">
            @if($partner->duration)
                <x-badge icon="schedule" :text="$partner->duration" />
            @endif
            @if($partner->level)
                <x-badge icon="signal_cellular_alt" :text="$partner->level" />
            @endif
            @if($partner->format)
                <x-badge icon="computer" :text="$partner->format" />
            @endif
        </div>
    </section>

    {{-- Description --}}
    <x-detail-section title="Tentang Program">
        <p class="font-body-lg text-body-lg text-text-secondary">{{ $partner->description }}</p>
    </x-detail-section>

    {{-- Informasi Pelatihan --}}
    <x-detail-section title="Informasi Pelatihan">
        <div class="flex flex-col gap-3">
            @if($partner->duration)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-fixed-dim flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[20px]">schedule</span>
                </div>
                <div>
                    <span class="font-body-sm text-body-sm text-text-secondary">Durasi</span>
                    <p class="font-body-lg text-body-lg text-text-primary">{{ $partner->duration }}</p>
                </div>
            </div>
            @endif
            @if($partner->level)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-fixed-dim flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[20px]">signal_cellular_alt</span>
                </div>
                <div>
                    <span class="font-body-sm text-body-sm text-text-secondary">Level</span>
                    <p class="font-body-lg text-body-lg text-text-primary">{{ $partner->level }}</p>
                </div>
            </div>
            @endif
            @if($partner->format)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-fixed-dim flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[20px]">computer</span>
                </div>
                <div>
                    <span class="font-body-sm text-body-sm text-text-secondary">Format</span>
                    <p class="font-body-lg text-body-lg text-text-primary">{{ $partner->format }}</p>
                </div>
            </div>
            @endif
        </div>
    </x-detail-section>

    {{-- Aksesibilitas --}}
    @if($partner->accessibility)
    <x-detail-section title="Aksesibilitas untuk Tunarungu">
        <div class="flex flex-col gap-3">
            @foreach($partner->accessibility as $acc)
                @php $accInfo = $accLabels[$acc] ?? ['label' => $acc, 'icon' => 'check']; @endphp
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-secondary-container flex items-center justify-center shrink-0 mt-0.5">
                        <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">{{ $accInfo['icon'] }}</span>
                    </div>
                    <div>
                        <p class="font-body-lg text-body-lg text-text-primary">{{ $accInfo['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-detail-section>
    @endif

    {{-- Yang Akan Dipelajari --}}
    @if($partner->outcomes)
    <x-detail-section title="Yang Akan Kamu Pelajari">
        <ul class="flex flex-col gap-3">
            @foreach($partner->outcomes as $outcome)
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-score-high-bg text-score-high-text flex items-center justify-center shrink-0 mt-0.5 text-[14px] font-bold">✓</span>
                    <span class="font-body-lg text-body-lg text-text-secondary">{{ $outcome }}</span>
                </li>
            @endforeach
        </ul>
    </x-detail-section>
    @endif

    {{-- CTA --}}
    <div class="mt-2 mb-4">
        @if($partner->website_url)
            <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer"
                class="w-full flex items-center justify-center bg-primary text-on-primary font-bold text-body-lg rounded-xl py-4 shadow-sm hover:brightness-110 active:scale-[0.98] transition-all min-h-touch-target-min cursor-pointer">
                <span class="material-symbols-outlined mr-2">open_in_new</span>
                Daftar Program
            </a>
        @else
            <div
                class="w-full flex items-center justify-center bg-surface-container text-on-surface-variant font-medium text-body-lg rounded-xl py-4 min-h-touch-target-min">
                <span class="material-symbols-outlined mr-2">hourglass_empty</span>
                Segera Hadir
            </div>
        @endif
    </div>
</div>
