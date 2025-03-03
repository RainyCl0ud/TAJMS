<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-blue-100">
        <!-- Container -->
        <div class="min-h-screen flex items-center justify-center p-6">
            <!-- Conditional Logo -->
            @if ($showLogo ?? false)
                <div class="absolute top-6">
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-black" />
                    </a>
                </div>
            @endif

            <div class="mt-[-30px]">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
