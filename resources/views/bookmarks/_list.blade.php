{{-- Bookmark collection rendered as a list (default) or grid (?view=grid). Expects a paginator in $bookmarks. --}}
@if ($bookmarks->isEmpty() && request()->filled('search'))
    <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-edge px-6 py-16 text-center">
        <x-icon name="search" class="mb-3 size-8 text-fg-faint" />
        <p class="font-medium text-fg-soft">No bookmarks match your search</p>
        <p class="mt-1 text-sm text-fg-subtle">Try a different word, or search the title, URL or note.</p>
        <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}" class="btn-secondary mt-4">Clear search</a>
    </div>
@elseif ($bookmarks->isEmpty())
    <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-edge px-6 py-16 text-center">
        <x-icon name="bookmark" class="mb-3 size-8 text-fg-faint" />
        <p class="font-medium text-fg-soft">No bookmarks here yet</p>
        <p class="mt-1 text-sm text-fg-subtle">Save your first link to get started.</p>
        <a href="{{ route('bookmarks.create', array_filter(['collection' => $newBookmarkCollectionId ?? null])) }}"
            class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-accent px-3 py-2 text-sm font-medium text-white hover:bg-accent-hover">
            <x-icon name="plus" /> Add bookmark
        </a>
    </div>
@elseif (request('view') === 'grid')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        @foreach ($bookmarks as $bookmark)
            <article class="group relative flex flex-col rounded-lg border border-edge bg-surface transition-colors hover:border-edge-strong">
                <x-favicon :bookmark="$bookmark" size="lg" />
                <div class="flex flex-1 flex-col gap-2 p-3">
                    <div class="flex items-start gap-1.5">
                        @if ($bookmark->is_favorite)
                            <x-icon name="star" class="mt-0.5 size-3.5 fill-amber-400 text-amber-400" />
                        @endif
                        <a href="{{ route('bookmarks.show', $bookmark) }}" class="line-clamp-2 text-sm font-medium text-fg after:absolute after:inset-0">{{ $bookmark->title }}</a>
                    </div>
                    @if ($bookmark->note)
                        <p class="line-clamp-2 text-xs text-fg-subtle">{{ $bookmark->note }}</p>
                    @endif
                    <div class="mt-auto flex items-end justify-between gap-2">
                        @include('bookmarks._meta')
                    </div>
                </div>
                <div class="absolute top-2 right-2 rounded-md bg-canvas/80 backdrop-blur transition-opacity focus-within:opacity-100 group-hover:opacity-100 pointer-fine:opacity-0">
                    @include('bookmarks._actions')
                </div>
            </article>
        @endforeach
    </div>
@else
    <div class="divide-y divide-edge/60">
        @foreach ($bookmarks as $bookmark)
            <article class="group relative -mx-3 flex items-center gap-3 rounded-lg px-3 py-2.5 transition-colors hover:bg-surface">
                <x-favicon :bookmark="$bookmark" />
                <div class="min-w-0 flex-1 space-y-0.5">
                    <div class="flex items-center gap-1.5">
                        @if ($bookmark->is_favorite)
                            <x-icon name="star" class="size-3.5 fill-amber-400 text-amber-400" />
                        @endif
                        <a href="{{ route('bookmarks.show', $bookmark) }}" class="truncate text-sm font-medium text-fg after:absolute after:inset-0">{{ $bookmark->title }}</a>
                    </div>
                    @include('bookmarks._meta')
                </div>
                @include('bookmarks._actions')
            </article>
        @endforeach
    </div>
@endif

<x-pagination :paginator="$bookmarks" />
