@props(['icon' => null, 'active' => false, 'count' => null])

<a {{ $attributes->class([
    'group flex items-center gap-2.5 rounded-md px-2.5 py-1.5 text-sm transition-colors',
    'bg-accent-soft font-medium text-accent-fg' => $active,
    'text-fg-muted hover:bg-hover hover:text-fg' => ! $active,
]) }}>
    @isset($leading)
        <span class="grid size-4 shrink-0 place-items-center">{{ $leading }}</span>
    @else
        <x-icon :name="$icon" @class(['size-4', 'text-accent' => $active]) />
    @endisset
    <span class="flex-1 truncate">{{ $slot }}</span>
    @if (! is_null($count))
        <span class="text-xs text-fg-subtle tabular-nums">{{ $count }}</span>
    @endif
</a>
