{{-- Breeze's <x-app-layout> renders inside the main sidebar layout. --}}
<x-layout :title="$attributes->get('title')">
    @isset($header)
        <div class="mb-6">{{ $header }}</div>
    @endisset

    {{ $slot }}
</x-layout>
