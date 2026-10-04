<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bookmarks') }}</title>
    <x-theme-script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-canvas text-fg-soft antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="{{ url('/') }}" class="mb-6 flex items-center gap-3">
            <x-application-logo class="size-10" />
            <span class="text-lg font-semibold text-fg">{{ config('app.name', 'Bookmarks') }}</span>
        </a>

        <div class="panel w-full max-w-md p-6 sm:p-8">
            {{ $slot }}
        </div>

        <x-theme-toggle class="mt-6 w-full max-w-64" />
    </div>
</body>
</html>
