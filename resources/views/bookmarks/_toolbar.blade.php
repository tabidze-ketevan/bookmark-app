{{-- View switcher: sort ("By date"), list/grid layout and CSV export. Expects $exportQuery. --}}
@php
    $currentSort = request('sort', 'newest');
    $currentView = request('view') === 'grid' ? 'grid' : 'list';
    $sortOptions = ['newest' => 'By date (newest)', 'oldest' => 'By date (oldest)', 'title' => 'By title (A–Z)'];
@endphp

<x-dropdown width="w-52">
    <x-slot:trigger>
        <span class="flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-sm text-fg-muted hover:bg-hover hover:text-fg">
            <x-icon name="calendar" />
            {{ $currentSort === 'title' ? 'By title' : 'By date' }}
            <x-icon name="chevron-down" class="size-3" />
        </span>
    </x-slot:trigger>

    @foreach ($sortOptions as $sortKey => $sortLabel)
        <x-dropdown-link :href="request()->fullUrlWithQuery(['sort' => $sortKey, 'page' => null])" :active="$currentSort === $sortKey">{{ $sortLabel }}</x-dropdown-link>
    @endforeach
</x-dropdown>

<div class="flex items-center rounded-md bg-surface p-0.5">
    <a href="{{ request()->fullUrlWithQuery(['view' => null]) }}" title="List"
        @class(['flex items-center gap-1.5 rounded px-2 py-1 text-sm', 'bg-surface text-fg shadow-sm dark:bg-surface-hover' => $currentView === 'list', 'text-fg-muted hover:text-fg' => $currentView !== 'list'])>
        <x-icon name="list" />
        <span class="hidden md:inline">List</span>
    </a>
    <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" title="Grid"
        @class(['flex items-center gap-1.5 rounded px-2 py-1 text-sm', 'bg-surface text-fg shadow-sm dark:bg-surface-hover' => $currentView === 'grid', 'text-fg-muted hover:text-fg' => $currentView !== 'grid'])>
        <x-icon name="grid" />
        <span class="hidden md:inline">Grid</span>
    </a>
</div>

<a href="{{ route('bookmarks.export', $exportQuery) }}" class="flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-sm text-fg-muted hover:bg-hover hover:text-fg">
    <x-icon name="download" />
    Export
</a>
