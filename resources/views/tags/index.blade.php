<x-layout title="Tags">
    <x-page-header title="Tags" icon="hashtag" :count="$tags->total()">
        <a href="{{ route('tags.create') }}" class="btn-primary"><x-icon name="plus" /> New tag</a>
    </x-page-header>

    @if ($tags->isEmpty())
        <div class="flex flex-col items-center rounded-xl border border-dashed border-edge px-6 py-16 text-center">
            <x-icon name="hashtag" class="mb-3 size-8 text-fg-faint" />
            <p class="font-medium text-fg-soft">No tags yet</p>
            <p class="mt-1 text-sm text-fg-subtle">Tags help you filter bookmarks across collections.</p>
        </div>
    @else
        <div class="panel divide-y divide-edge">
            @foreach ($tags as $tag)
                <article class="group relative flex items-center gap-3 px-4 py-3 transition-colors first:rounded-t-xl last:rounded-b-xl hover:bg-surface-hover">
                    <x-tag-dot :tag="$tag" class="size-2.5" />
                    <a href="{{ route('bookmarks.index', ['tag' => $tag]) }}" class="min-w-0 flex-1 truncate text-sm font-medium text-fg after:absolute after:inset-0">{{ $tag->title }}</a>
                    <span class="text-xs text-fg-subtle tabular-nums">{{ trans_choice(':count bookmark|:count bookmarks', $tag->bookmarks_count) }}</span>
                    <div class="relative z-10 flex items-center gap-0.5 transition-opacity focus-within:opacity-100 group-hover:opacity-100 pointer-fine:opacity-0">
                        <a href="{{ route('tags.edit', $tag) }}" title="Edit tag" class="rounded-md p-1.5 text-fg-muted hover:bg-hover hover:text-fg">
                            <x-icon name="pencil" />
                        </a>
                        <x-delete-button :action="route('tags.destroy', $tag)" confirm="Delete this tag? It will be removed from all bookmarks." />
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <x-pagination :paginator="$tags" />
</x-layout>
