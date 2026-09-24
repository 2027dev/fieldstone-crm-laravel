<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full">
<div class="grid min-h-screen lg:grid-cols-2">
    <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16">
        <a href="{{ route('home') }}" class="mb-10 flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-base font-black text-white">F</span>
            <span class="text-xl font-extrabold tracking-tight text-navy-900">Fieldstone CRM</span>
        </a>

        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

    <div class="relative hidden overflow-hidden bg-navy-900 p-16 lg:flex lg:flex-col lg:justify-center">
        <blockquote class="relative z-10 max-w-md">
            <p class="text-[26px] leading-snug font-bold text-white">
                Every call, email and deal in one pipeline your team actually wants to use.
            </p>
            <footer class="mt-6 flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-indigo-brand text-sm font-bold text-white">AS</span>
                <div>
                    <p class="text-sm font-semibold text-white">Avery Sinclair</p>
                    <p class="text-sm text-white/60">Head of Sales, Fieldstone Partners</p>
                </div>
            </footer>
        </blockquote>

        <div class="absolute -right-24 -bottom-24 size-96 rounded-full bg-indigo-brand/30 blur-3xl"></div>
        <div class="absolute -top-20 -left-20 size-72 rounded-full bg-brand-600/30 blur-3xl"></div>
    </div>
</div>
</body>
</html>
