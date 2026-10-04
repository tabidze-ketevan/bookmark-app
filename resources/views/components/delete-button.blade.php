@props(['action', 'confirm' => 'Are you sure you want to delete this?', 'label' => 'Delete'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm))" class="inline-flex">
    @csrf
    @method('DELETE')
    <button type="submit" title="{{ $label }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md p-1.5 text-fg-muted hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400']) }}>
        <x-icon name="trash" />
        @if ($slot->isNotEmpty())
            <span class="text-sm">{{ $slot }}</span>
        @endif
    </button>
</form>
