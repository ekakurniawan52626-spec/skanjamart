<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SKANJAMart') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-hero-gradient">
            <div class="mb-2">
                <a href="{{ route('home') }}" class="font-display font-semibold text-2xl text-forest-700 tracking-tight">
                    SKANJA<span class="text-brass-500 italic">Mart</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 card-soft !bg-white">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
