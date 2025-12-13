<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Realt Bank') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Realt Bank CRM</h1>
                        </div>
                        @if (Route::has('login'))
                            <livewire:welcome.navigation />
                        @endif
                    </header>

                    <main class="mt-6">
                        <div class="flex flex-col items-center justify-center py-16">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
                                Ласкаво просимо до Realt Bank CRM
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-8">
                                Система управління нерухомістю
                            </p>
                            @auth
                                <a href="{{ route('properties.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Перейти до CRM
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Увійти
                                </a>
                            @endauth
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
