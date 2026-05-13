<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Equaly</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-linear-to-br from-bg-main to-surface-container-low min-h-screen text-text-primary font-body-lg">
    <div class="max-w-container-max mx-auto min-h-screen relative pb-20">

        <header class="fixed top-0 left-0 right-0 z-50 flex justify-center items-center px-4 h-16 max-w-md mx-auto bg-surface/80 backdrop-blur-md dark:bg-gray-900 border-b border-border-subtle shadow-sm transition-colors duration-200">
            <span class="font-h1 text-h1 text-text-primary tracking-tight">Equaly</span>
        </header>

        <main class="pt-22 px-gutter flex flex-col">
            {{ $slot }}
        </main>

        <nav
            class="fixed bottom-0 left-0 right-0 w-full z-50 flex justify-around items-center bg-surface dark:bg-gray-900 px-2 py-3 max-w-container-max mx-auto border-t border-border-subtle shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
            <a href="{{ route('beranda') }}" wire:navigate
                class="flex flex-col items-center justify-center py-1 min-w-touch-target-min min-h-touch-target-min active:scale-95 transition-transform {{ request()->routeIs('beranda') ? 'text-primary' : 'text-secondary hover:text-primary' }}">
                <span class="material-symbols-outlined"
                    style="font-variation-settings: 'FILL' {{ request()->routeIs('beranda') ? 1 : 0 }};">home</span>
                <span class="text-[12px] font-medium mt-1">Beranda</span>
            </a>
            <a href="{{ route('lowongan') }}" wire:navigate
                class="flex flex-col items-center justify-center py-1 min-w-touch-target-min min-h-touch-target-min active:scale-95 transition-transform {{ request()->routeIs('lowongan') ? 'text-primary' : 'text-secondary hover:text-primary' }}">
                <span class="material-symbols-outlined"
                    style="font-variation-settings: 'FILL' {{ request()->routeIs('lowongan') ? 1 : 0 }};">work</span>
                <span class="text-[12px] font-medium mt-1">Lowongan</span>
            </a>
            <a href="{{ route('academy') }}" wire:navigate
                class="flex flex-col items-center justify-center py-1 min-w-touch-target-min min-h-touch-target-min active:scale-95 transition-transform {{ request()->routeIs('academy') ? 'text-primary' : 'text-secondary hover:text-primary' }}">
                <span class="material-symbols-outlined"
                    style="font-variation-settings: 'FILL' {{ request()->routeIs('academy') ? 1 : 0 }};">school</span>
                <span class="text-[12px] font-medium mt-1">Academy</span>
            </a>
            <a href="{{ route('profil') }}" wire:navigate
                class="flex flex-col items-center justify-center py-1 min-w-touch-target-min min-h-touch-target-min active:scale-95 transition-transform {{ request()->routeIs('profil') ? 'text-primary' : 'text-secondary hover:text-primary' }}">
                <span class="material-symbols-outlined"
                    style="font-variation-settings: 'FILL' {{ request()->routeIs('profil') ? 1 : 0 }};">person</span>
                <span class="text-[12px] font-medium mt-1">Profil</span>
            </a>
        </nav>

    </div>
</body>

</html>
