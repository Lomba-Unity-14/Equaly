@props(['label', 'name', 'type' => 'text', 'placeholder' => '', 'error' => null, 'icon' => null])

<div class="flex flex-col gap-1.5">
    <div class="flex justify-between items-center px-1">
        <label for="{{ $name }}" class="font-body-sm text-body-sm text-text-primary font-medium">{{ $label }}</label>
        @isset($labelEnd)
            <div class="text-xs font-semibold">{{ $labelEnd }}</div>
        @endisset
    </div>
    <div class="relative">
        @if($icon)
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-xl pointer-events-none">{{ $icon }}</span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name) }}"
            placeholder="{{ $placeholder }}"
            class="bg-surface border {{ $error ? 'border-error' : 'border-border-subtle' }} rounded-xl {{ $icon ? 'pl-12' : 'pl-4' }} @isset($trailing) pr-12 @else pr-4 @endisset py-3 font-body-lg text-body-lg text-text-primary placeholder:text-secondary focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors min-h-touch-target-min w-full"
        />

        @isset($trailing)
            <div class="absolute right-4 top-1/2 -translate-y-1/2">
                {{ $trailing }}
            </div>
        @endisset
    </div>
    @if($error)
        <p class="font-body-sm text-body-sm text-error">{{ $error }}</p>
    @endif
</div>
