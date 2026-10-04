@props(['align' => 'right', 'width' => 'w-56'])

{{-- Native <details> dropdown: works without JavaScript. --}}
<details {{ $attributes->merge(['class' => 'group relative']) }} data-dropdown>
    <summary class="cursor-pointer select-none">{{ $trigger }}</summary>
    <div @class([
        'absolute z-50 mt-2 overflow-hidden rounded-lg border border-edge bg-surface py-1 shadow-xl shadow-black/10 dark:shadow-2xl dark:shadow-black/50',
        $width,
        'right-0' => $align === 'right',
        'left-0' => $align === 'left',
    ])>
        {{ $slot }}
    </div>
</details>
