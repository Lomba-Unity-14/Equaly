<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Equaly - Lengkapi Profil</title>

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
    <div class="max-w-container-max mx-auto min-h-screen relative">

        <main class="pt-8 px-gutter pb-8 flex flex-col">
            {{ $slot }}
        </main>

    </div>

    <div class="fixed top-0 left-0 w-full h-full -z-10 pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-fixed-dim opacity-20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-secondary-fixed opacity-30 rounded-full blur-[100px]"></div>
    </div>

    @stack('scripts')
</body>

</html>
