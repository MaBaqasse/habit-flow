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
    <body class="font-sans text-text-primary antialiased bg-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 sm:bg-bg-light">
            <div class="mb-8 sm:mb-12">
                <a href="/" class="flex justify-center">
                    <x-application-logo class="w-20 h-20 fill-current text-brand-primary" />
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 sm:px-8 sm:py-10 bg-white shadow-lg sm:rounded-softer border border-bg-light">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
