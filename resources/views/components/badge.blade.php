@props([
    'icon' => null,
    'text',
    'variant' => 'default',
])

@php
    $variantClasses = [
        'high' => 'bg-score-high-bg text-score-high-text',
        'medium' => 'bg-score-medium-bg text-score-medium-text',
        'low' => 'bg-score-low-bg text-score-low-text',
        'default' => 'bg-surface-container text-on-surface-variant',
    ];
    $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<div class="inline-flex items-center px-3 py-1.5 rounded-full {{ $variantClass }} font-label-caps text-label-caps">
    @if($icon)
        <span class="material-symbols-outlined text-[16px] mr-1" @if($variant !== 'default') style="font-variation-settings: 'FILL' 1;" @endif>{{ $icon }}</span>
    @endif
    {{ $text }}
</div>
