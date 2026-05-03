@props(['label', 'name', 'type' => 'text', 'placeholder' => '', 'error' => null])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="font-body-sm text-body-sm text-text-primary font-medium">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name) }}"
        placeholder="{{ $placeholder }}"
        class="bg-surface border {{ $error ? 'border-error' : 'border-border-subtle' }} rounded-xl px-4 py-3 font-body-lg text-body-lg text-text-primary placeholder:text-secondary focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors min-h-touch-target-min"
    />
    @if($error)
        <p class="font-body-sm text-body-sm text-error">{{ $error }}</p>
    @endif
</div>
