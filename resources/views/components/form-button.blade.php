@props(['type' => 'submit', 'variant' => 'primary'])

@php
$base = 'w-full flex items-center justify-center rounded-xl py-3 px-8 font-label-caps text-label-caps font-bold active:scale-[0.98] transition-all min-h-touch-target-min';
$primary = 'bg-primary text-on-primary hover:bg-primary-container';
$secondary = 'bg-surface-container text-on-surface hover:bg-surface-variant border border-border-subtle';
@endphp

<button type="{{ $type }}" class="{{ $base }} {{ $variant === 'primary' ? $primary : $secondary }}">
    {{ $slot }}
</button>
