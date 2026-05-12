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
        'bisindo' => ['label' => 'BISINDO', 'icon' => 'sign_language'],
        'sibi' => ['label' => 'SIBI', 'icon' => 'sign_language'],
        'teks_tertulis' => ['label' => 'Teks', 'icon' => 'chat'],
        'juru_isyarat' => ['label' => 'Juru Isyarat', 'icon' => 'accessibility'],
        'notifikasi_visual' => ['label' => 'Notif Visual', 'icon' => 'notifications'],
    ];

    $visualGradients = [
        'it_programming' => 'from-primary-fixed-dim/20 to-primary-container/15',
        'desain_kreatif' => 'from-tertiary-fixed-dim/30 to-tertiary-container/10',
        'general' => 'from-primary-container/15 to-secondary-container/40',
    ];
    $visualGradient = $visualGradients[$partner->category] ?? $visualGradients['general'];
@endphp

<a href="{{ route('academy.detail', $partner->id) }}" wire:navigate
    class="block bg-surface rounded-2xl border border-outline-variant shadow-sm overflow-hidden group hover:shadow-md transition-all active:scale-[0.98]">
    {{-- Visual Area --}}
    <div class="relative h-36 bg-linear-to-br {{ $visualGradient }} overflow-hidden">
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-14 h-14 rounded-2xl bg-surface/70 backdrop-blur-sm flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-primary text-[32px]" style="font-variation-settings: 'FILL' 1;">{{ $typeInfo['icon'] }}</span>
            </div>
        </div>
        @if($partner->accessibility)
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                @foreach($partner->accessibility as $acc)
                    @php $info = $accLabels[$acc] ?? ['label' => $acc, 'icon' => 'check']; @endphp
                    <span class="inline-flex items-center gap-1 bg-score-high-bg text-score-high-text text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        {{ $info['label'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
    {{-- Content --}}
    <div class="p-4 space-y-2">
        <div class="flex items-center justify-between gap-2">
            <span class="text-label-caps text-text-secondary truncate">
                {{ $categoryLabel }}{{ $partner->duration && $categoryLabel ? ' · ' : '' }}{{ $partner->duration ?? '' }}
            </span>
            <x-badge :icon="$typeInfo['icon']" :text="$typeInfo['label']" :variant="$typeInfo['variant']" />
        </div>
        <h3 class="font-h2 text-h2 text-text-primary leading-snug">{{ $partner->name }}</h3>
        @if($partner->description)
            <p class="text-body-sm text-text-secondary line-clamp-2">{{ $partner->description }}</p>
        @endif
    </div>
</a>
