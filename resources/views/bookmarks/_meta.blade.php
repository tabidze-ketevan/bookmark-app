{{-- Collection label · domain · timestamp · tags for a single bookmark. --}}
<div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1 text-xs text-fg-subtle">
    @if ($bookmark->relationLoaded('collection'))
        @if ($bookmark->collection)
            <a href="{{ route('bookmarks.index', ['collection' => $bookmark->collection]) }}" class="relative z-10 inline-flex items-center gap-1 text-fg-muted hover:text-fg">
                <x-icon name="folder" class="size-3.5" />{{ $bookmark->collection->title }}
            </a>
        @else
            <a href="{{ route('bookmarks.index', ['filter' => 'unsorted']) }}" class="relative z-10 inline-flex items-center gap-1 text-fg-muted hover:text-fg">
                <x-icon name="inbox" class="size-3.5" />Unsorted
            </a>
        @endif
        <span aria-hidden="true">·</span>
    @endif

    <span class="truncate">{{ $bookmark->domain }}</span>
    <span aria-hidden="true">·</span>
    <time datetime="{{ $bookmark->created_at?->toIso8601String() }}" title="{{ $bookmark->created_at?->format('M j, Y g:i A') }}">{{ $bookmark->addedAtLabel() }}</time>

    @if ($bookmark->relationLoaded('tags') && $bookmark->tags->isNotEmpty())
        <span class="flex flex-wrap items-center gap-1">
            @foreach ($bookmark->tags as $tag)
                <x-tag-pill :tag="$tag" :href="route('bookmarks.index', ['tag' => $tag])" class="relative z-10" />
            @endforeach
        </span>
    @endif
</div>
