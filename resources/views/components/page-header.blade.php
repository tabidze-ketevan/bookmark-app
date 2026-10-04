@props(['title', 'icon' => null, 'count' => null])

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex min-w-0 items-center gap-2.5">
        @if ($icon)
            <x-icon :name="$icon" class="size-5 text-fg-muted" />
        @endif
        <h1 class="truncate text-xl font-semibold text-fg">{{ $title }}</h1>
        @if (! is_null($count))
            <span class="text-sm text-fg-subtle tabular-nums">{{ $count }}</span>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-1">{{ $slot }}</div>
    @endif
</div>
