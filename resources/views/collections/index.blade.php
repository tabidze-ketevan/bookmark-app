<x-layout title="Collections">
    <x-page-header title="Collections" icon="folder" :count="$collections->total()">
        <a href="{{ route('collections.create') }}" class="btn-primary"><x-icon name="plus" /> New collection</a>
    </x-page-header>

    @if ($collections->isEmpty())
        <div class="flex flex-col items-center rounded-xl border border-dashed border-edge px-6 py-16 text-center">
            <x-icon name="folder" class="mb-3 size-8 text-fg-faint" />
            <p class="font-medium text-fg-soft">No collections yet</p>
            <p class="mt-1 text-sm text-fg-subtle">Group related bookmarks into collections.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($collections as $collection)
                <article class="group relative flex items-center gap-3 rounded-lg border border-edge bg-surface p-4 transition-colors hover:border-edge-strong">
                    <span class="grid size-10 shrink-0 place-items-center rounded-md bg-accent-soft text-accent">
                        <x-icon name="folder" class="size-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('bookmarks.index', ['collection' => $collection]) }}" class="block truncate font-medium text-fg after:absolute after:inset-0">{{ $collection->title }}</a>
                        <p class="text-xs text-fg-subtle">{{ trans_choice(':count bookmark|:count bookmarks', $collection->bookmarks_count) }}</p>
                    </div>
                    <div class="relative z-10 flex items-center gap-0.5 transition-opacity focus-within:opacity-100 group-hover:opacity-100 pointer-fine:opacity-0">
                        <a href="{{ route('collections.edit', $collection) }}" title="Edit collection" class="rounded-md p-1.5 text-fg-muted hover:bg-hover hover:text-fg">
                            <x-icon name="pencil" />
                        </a>
                        <x-delete-button :action="route('collections.destroy', $collection)" confirm="Delete this collection? Its bookmarks will be moved to Unsorted." />
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <x-pagination :paginator="$collections" />
</x-layout>
