<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Equaly - Detail Academy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body
    class="bg-linear-to-br from-bg-main to-surface-container-low min-h-screen text-text-primary font-body-lg">
    <div class="max-w-container-max mx-auto min-h-screen relative">

        <header
            class="fixed top-0 left-0 right-0 w-full max-w-container-max mx-auto bg-surface/80 backdrop-blur-md border-b border-border-subtle shadow-sm flex items-center px-4 h-16 z-50">
            <a href="{{ route('academy') }}" wire:navigate
                class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-surface-container-low active:scale-95 transition-transform"
                aria-label="Kembali">
                <span class="material-symbols-outlined text-primary">arrow_back</span>
            </a>
            <h1 class="ml-4 font-body-lg text-body-lg font-semibold text-text-primary">Detail Academy</h1>
        </header>

        <main class="pt-22 px-gutter pb-4 flex flex-col">
            {{ $slot }}
        </main>

    </div>
</body>

</html>
