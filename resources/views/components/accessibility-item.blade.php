@props([
    'icon',
    'title',
    'description',
])

<div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low">
    <span class="material-symbols-outlined text-primary mt-0.5">{{ $icon }}</span>
    <div>
        <p class="font-body-lg text-body-lg text-text-primary font-semibold">{{ $title }}</p>
        <p class="font-body-sm text-body-sm text-text-secondary">{{ $description }}</p>
    </div>
</div>
