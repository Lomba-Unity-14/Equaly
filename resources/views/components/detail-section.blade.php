@props([
    'title' => null,
])

<section class="bg-surface rounded-2xl border border-outline-variant shadow-sm p-6">
    @if($title)
        <h3 class="font-h2 text-h2 text-text-primary mb-stack-md">{{ $title }}</h3>
    @endif
    {{ $slot }}
</section>
