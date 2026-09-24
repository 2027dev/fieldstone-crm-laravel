<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div class="flex h-full" x-data="{ mobileNav: false }">
    <x-sidebar />

    <div class="flex min-w-0 flex-1 flex-col">
        <x-topbar :title="$title ?? config('app.name')" :breadcrumb="$breadcrumb ?? null" />

        <div class="flex min-h-0 flex-1">
            @isset($subnav)
                <aside class="hidden w-60 shrink-0 border-r border-line bg-white p-3 lg:block">
                    {{ $subnav }}
                </aside>
            @endisset

            <main class="min-w-0 flex-1 overflow-y-auto">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>
</div>

<x-quick-create />
</body>
</html>
