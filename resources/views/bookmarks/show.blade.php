<x-layout :title="$bookmark->title">
    <a href="{{ route('bookmarks.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm text-fg-subtle hover:text-fg-soft">
        <x-icon name="arrow-left" /> All bookmarks
    </a>

    <article class="panel max-w-3xl overflow-hidden">
        <div class="flex flex-wrap items-start gap-4 border-b border-edge p-6">
            <x-favicon :bookmark="$bookmark" class="size-14!" />
            <div class="min-w-0 flex-1">
                <h1 class="flex items-center gap-2 text-xl font-semibold text-fg">
                    @if ($bookmark->is_favorite)
                        <x-icon name="star" class="size-5 fill-amber-400 text-amber-400" />
                    @endif
                    <span class="break-words">{{ $bookmark->title }}</span>
                </h1>
                <div class="mt-1.5">@include('bookmarks._meta')</div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                    <x-icon name="external" /> Open
                </a>
                <a href="{{ route('bookmarks.edit', $bookmark) }}" class="btn-secondary">
                    <x-icon name="pencil" /> Edit
                </a>
                <x-delete-button :action="route('bookmarks.destroy', $bookmark)" confirm="Delete this bookmark?" class="px-3 py-2">Delete</x-delete-button>
            </div>
        </div>

        <dl class="divide-y divide-edge text-sm">
            <div class="grid gap-1 px-6 py-4 sm:grid-cols-4">
                <dt class="text-fg-subtle">URL</dt>
                <dd class="break-all sm:col-span-3"><a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" class="text-accent hover:text-accent-fg">{{ $bookmark->url }}</a></dd>
            </div>
            <div class="grid gap-1 px-6 py-4 sm:grid-cols-4">
                <dt class="text-fg-subtle">Note</dt>
                <dd class="whitespace-pre-line text-fg-soft sm:col-span-3">{{ $bookmark->note ?: '—' }}</dd>
            </div>
            <div class="grid gap-1 px-6 py-4 sm:grid-cols-4">
                <dt class="text-fg-subtle">Added</dt>
                <dd class="text-fg-soft sm:col-span-3">{{ $bookmark->created_at?->format('M j, Y \a\t g:i A') }}</dd>
            </div>
        </dl>
    </article>
</x-layout>
