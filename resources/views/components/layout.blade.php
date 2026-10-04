@props(['title' => null])

@php
    $onBookmarkIndex = request()->routeIs('bookmarks.index');
    $activeFilter = $onBookmarkIndex ? request()->query('filter') : null;
    $activeCollectionId = $onBookmarkIndex ? request()->integer('collection') : 0;
    $activeTagId = $onBookmarkIndex ? request()->integer('tag') : 0;
    $isUnfilteredList = $onBookmarkIndex && ! $activeFilter && ! $activeCollectionId && ! $activeTagId;

    /** Keep the active sidebar scope while searching, e.g. search inside the selected collection. */
    $searchScope = $onBookmarkIndex ? array_filter(request()->only(['filter', 'collection', 'tag', 'sort', 'view'])) : [];
    $scopedCollection = $activeCollectionId ? $sidebarCollections->firstWhere('id', $activeCollectionId) : null;
    $scopedTag = $activeTagId ? $sidebarTags->firstWhere('id', $activeTagId) : null;
    $searchPlaceholder = match (true) {
        $scopedCollection !== null => 'Search in '.$scopedCollection->title,
        $scopedTag !== null => 'Search in #'.$scopedTag->title,
        $activeFilter === 'unsorted' => 'Search in Unsorted',
        $activeFilter === 'favorites' => 'Search in Favorites',
        default => 'Search bookmarks',
    };
    $newBookmarkCollectionId = $scopedCollection?->id ?? request()->route('collection')?->id;
    $userInitials = str(auth()->user()->name)->explode(' ')->take(2)->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name', 'Bookmarks') }}</title>
    <x-theme-script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-canvas text-fg-soft antialiased">
    <input type="checkbox" id="sidebar-toggle" class="peer sr-only">

    {{-- Mobile backdrop --}}
    <label for="sidebar-toggle" class="fixed inset-0 z-30 hidden bg-black/60 peer-checked:block lg:peer-checked:hidden" aria-hidden="true"></label>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-edge bg-sidebar transition-transform peer-checked:translate-x-0 lg:translate-x-0">
        <div class="flex items-center justify-between gap-2 px-3 pt-3 pb-2">
            <x-dropdown align="left" width="w-56" class="min-w-0 flex-1">
                <x-slot:trigger>
                    <span class="flex items-center gap-2.5 rounded-md px-2 py-1.5 hover:bg-hover">
                        <span class="grid size-7 shrink-0 place-items-center rounded-full bg-gradient-to-br from-accent to-violet-500 text-xs font-semibold text-white">{{ $userInitials }}</span>
                        <span class="truncate text-sm font-medium text-fg">{{ auth()->user()->name }}</span>
                        <x-icon name="chevron-down" class="size-3.5 text-fg-subtle transition-transform group-open:rotate-180" />
                    </span>
                </x-slot:trigger>

                <div class="border-b border-edge px-3 py-2.5">
                    <p class="truncate text-sm font-medium text-fg">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-fg-subtle">{{ auth()->user()->email }}</p>
                </div>
                <x-dropdown-link :href="route('profile.edit')" icon="user" :active="request()->routeIs('profile.*')">Profile</x-dropdown-link>
                <div class="my-1 border-t border-edge"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-sm text-fg-soft hover:bg-surface-hover">
                        <x-icon name="logout" class="size-4 text-fg-muted" />
                        Log out
                    </button>
                </form>
            </x-dropdown>

            <label for="sidebar-toggle" class="cursor-pointer rounded-md p-1.5 text-fg-muted hover:bg-hover lg:hidden">
                <x-icon name="x" class="size-5" />
                <span class="sr-only">Close menu</span>
            </label>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 pb-6">
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('bookmarks.index')" icon="bookmark" :count="$sidebarCounts['all']"
                    :active="$isUnfilteredList">All bookmarks</x-sidebar-link>
                <x-sidebar-link :href="route('bookmarks.index', ['filter' => 'unsorted'])" icon="inbox" :count="$sidebarCounts['unsorted']"
                    :active="$activeFilter === 'unsorted'">Unsorted</x-sidebar-link>
            </div>

            <div>
                <div class="mb-1 flex items-center justify-between px-2.5">
                    <a href="{{ route('collections.index') }}" class="text-xs font-semibold tracking-wider text-fg-subtle uppercase hover:text-fg-soft">Collections</a>
                    <a href="{{ route('collections.create') }}" class="rounded p-0.5 text-fg-subtle hover:bg-hover hover:text-fg" title="New collection">
                        <x-icon name="plus" class="size-3.5" />
                    </a>
                </div>
                <div class="space-y-0.5">
                    @forelse ($sidebarCollections as $sidebarCollection)
                        <x-sidebar-link :href="route('bookmarks.index', ['collection' => $sidebarCollection])" icon="folder" :count="$sidebarCollection->bookmarks_count"
                            :active="$activeCollectionId === $sidebarCollection->id">{{ $sidebarCollection->title }}</x-sidebar-link>
                    @empty
                        <a href="{{ route('collections.create') }}" class="block px-2.5 py-1.5 text-sm text-fg-subtle hover:text-fg-soft">+ Create a collection</a>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="mb-1 flex items-center justify-between px-2.5">
                    <a href="{{ route('tags.index') }}" class="text-xs font-semibold tracking-wider text-fg-subtle uppercase hover:text-fg-soft">Filters</a>
                    <a href="{{ route('tags.create') }}" class="rounded p-0.5 text-fg-subtle hover:bg-hover hover:text-fg" title="New tag">
                        <x-icon name="plus" class="size-3.5" />
                    </a>
                </div>
                <div class="space-y-0.5">
                    <x-sidebar-link :href="route('bookmarks.index', ['filter' => 'favorites'])" icon="star" :count="$sidebarCounts['favorites']"
                        :active="$activeFilter === 'favorites'">Favorites</x-sidebar-link>
                    @foreach ($sidebarTags as $sidebarTag)
                        <x-sidebar-link :href="route('bookmarks.index', ['tag' => $sidebarTag])" :count="$sidebarTag->bookmarks_count"
                            :active="$activeTagId === $sidebarTag->id">
                            <x-slot:leading><x-tag-dot :tag="$sidebarTag" /></x-slot:leading>
                            {{ $sidebarTag->title }}
                        </x-sidebar-link>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="border-t border-edge p-3">
            <x-theme-toggle />
        </div>
    </aside>

    {{-- Main column --}}
    <div class="flex min-h-screen flex-col lg:pl-64">
        <header class="sticky top-0 z-20 flex items-center gap-2 border-b border-edge bg-canvas/90 px-4 py-2.5 backdrop-blur lg:px-8">
            <label for="sidebar-toggle" class="cursor-pointer rounded-md p-1.5 text-fg-muted hover:bg-hover lg:hidden">
                <x-icon name="menu" class="size-5" />
                <span class="sr-only">Open menu</span>
            </label>

            <form method="GET" action="{{ route('bookmarks.index') }}" class="relative min-w-0 flex-1" role="search">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-fg-subtle" />
                @foreach ($searchScope as $scopeKey => $scopeValue)
                    <input type="hidden" name="{{ $scopeKey }}" value="{{ $scopeValue }}">
                @endforeach
                <input type="search" name="search" value="{{ $onBookmarkIndex ? request('search') : '' }}" placeholder="{{ $searchPlaceholder }}" aria-label="Search bookmarks"
                    maxlength="255" class="w-full max-w-xl rounded-lg border border-transparent bg-surface py-2 pr-3 pl-9 text-sm text-fg placeholder:text-fg-subtle focus:border-accent focus:outline-none">
            </form>

            <x-dropdown width="w-72">
                <x-slot:trigger>
                    <span class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-fg-soft hover:bg-hover">
                        <x-icon name="sparkles" class="text-violet-400" />
                        <span class="hidden sm:inline">Ask</span>
                    </span>
                </x-slot:trigger>

                <div class="space-y-3 p-3">
                    <div class="flex items-center gap-2 text-sm font-medium text-fg">
                        <x-icon name="sparkles" class="text-violet-400" />
                        Ask about your bookmarks
                    </div>
                    <input type="text" disabled placeholder="e.g. Find my design resources"
                        class="w-full cursor-not-allowed rounded-md border border-edge bg-canvas px-3 py-2 text-sm placeholder:text-fg-faint">
                    <p class="text-xs text-fg-subtle">AI assistant is coming soon.</p>
                </div>
            </x-dropdown>

            <x-dropdown width="w-52">
                <x-slot:trigger>
                    <span class="flex items-center gap-1.5 rounded-lg bg-accent px-3 py-2 text-sm font-medium text-white hover:bg-accent-hover">
                        <x-icon name="plus" />
                        <span class="hidden sm:inline">Add</span>
                        <x-icon name="chevron-down" class="size-3.5 opacity-70" />
                    </span>
                </x-slot:trigger>

                <x-dropdown-link :href="route('bookmarks.create', array_filter(['collection' => $newBookmarkCollectionId]))" icon="bookmark">Bookmark</x-dropdown-link>
                <x-dropdown-link :href="route('collections.create')" icon="folder">Collection</x-dropdown-link>
                <x-dropdown-link :href="route('tags.create')" icon="hashtag">Tag</x-dropdown-link>
            </x-dropdown>
        </header>

        <main class="flex-1 px-4 py-6 lg:px-8">
            <x-flash />

            {{ $slot }}
        </main>
    </div>
</body>
</html>
