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

    $accIcons = [
        'bisindo' => 'sign_language',
        'sibi' => 'sign_language',
        'teks_tertulis' => 'chat',
        'juru_isyarat' => 'accessibility',
        'notifikasi_visual' => 'notifications',
    ];
@endphp

<a href="{{ route('academy.detail', $partner->id) }}" wire:navigate
    class="block bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 hover:shadow-md transition-shadow active:scale-[0.98]">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary-fixed-dim flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-primary text-[22px]" style="font-variation-settings: 'FILL' 1;">{{ $typeInfo['icon'] }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-body-lg text-body-lg font-semibold text-text-primary leading-tight">{{ $partner->name }}</h3>
            <p class="font-body-sm text-body-sm text-text-secondary">{{ $categoryLabel }}</p>
        </div>
        <span class="material-symbols-outlined text-outline shrink-0 mt-1">chevron_right</span>
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-3">
        @if($partner->duration)
            <span class="font-label-caps text-label-caps text-outline flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">schedule</span>
                {{ $partner->duration }}
            </span>
        @endif
        @if($partner->level)
            <span class="font-label-caps text-label-caps text-outline">·</span>
            <span class="font-label-caps text-label-caps text-outline flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">signal_cellular_alt</span>
                {{ $partner->level }}
            </span>
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-2">
        <x-badge :icon="$typeInfo['icon']" :text="$typeInfo['label']" :variant="$typeInfo['variant']" />
        @if($partner->accessibility)
            @foreach($partner->accessibility as $acc)
                <span class="material-symbols-outlined text-[18px] text-outline" title="{{ $acc }}">{{ $accIcons[$acc] ?? 'check' }}</span>
            @endforeach
        @endif
    </div>
</a>
