<x-layout :title="$heading">
    <x-page-header :title="$heading" :icon="$headingIcon" :count="$bookmarks->total()">
        @include('bookmarks._toolbar')

        @if ($currentCollection || $currentTag)
            @php
                [$resourceRoute, $resourceModel, $resourceLabel, $deleteConfirm] = $currentCollection
                    ? ['collections', $currentCollection, 'collection', 'Delete this collection? Its bookmarks will be moved to Unsorted.']
                    : ['tags', $currentTag, 'tag', 'Delete this tag? It will be removed from all bookmarks.'];
            @endphp
            <span class="mx-1 h-5 w-px bg-edge" aria-hidden="true"></span>
            <a href="{{ route($resourceRoute.'.edit', $resourceModel) }}" title="Edit {{ $resourceLabel }}" class="rounded-md p-1.5 text-fg-muted hover:bg-hover hover:text-fg">
                <x-icon name="pencil" />
            </a>
            <x-delete-button :action="route($resourceRoute.'.destroy', $resourceModel)" :confirm="$deleteConfirm" />
        @endif
    </x-page-header>

    @if ($search)
        <div class="-mt-2 mb-5 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-accent/30 bg-accent-soft py-1 pr-1 pl-3 text-fg">
                <x-icon name="search" class="size-3.5 text-accent" />
                <span>“{{ $search }}”</span>
                <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}" title="Clear search" class="rounded-full p-0.5 text-fg-muted hover:bg-hover-strong hover:text-fg">
                    <x-icon name="x" class="size-3.5" />
                </a>
            </span>
            <span class="text-fg-subtle">
                {{ trans_choice(':count result|:count results', $bookmarks->total()) }}
                @if ($heading !== 'All bookmarks')
                    in {{ $heading }} ·
                    <a href="{{ route('bookmarks.index', ['search' => $search]) }}" class="text-accent hover:text-accent-fg">Search all bookmarks</a>
                @endif
            </span>
        </div>
    @endif

    @include('bookmarks._list', ['newBookmarkCollectionId' => $currentCollection?->id])
</x-layout>
