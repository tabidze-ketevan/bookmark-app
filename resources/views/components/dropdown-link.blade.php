@props(['icon' => null, 'active' => false])

<a {{ $attributes->class([
    'flex items-center gap-2.5 px-3 py-2 text-sm hover:bg-surface-hover',
    'text-fg' => $active,
    'text-fg-soft' => ! $active,
]) }}>
    @if ($icon)
        <x-icon :name="$icon" class="size-4 text-fg-muted" />
    @endif
    <span class="flex-1">{{ $slot }}</span>
    @if ($active)
        <x-icon name="check" class="size-4 text-accent" />
    @endif
</a>
