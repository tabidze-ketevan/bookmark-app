@php
    $themeOptions = ['light' => ['Light', 'sun'], 'dark' => ['Dark', 'moon'], 'system' => ['System', 'computer']];
@endphp

<div x-data="themeToggle" {{ $attributes->merge(['class' => 'flex items-center gap-0.5 rounded-lg bg-hover p-0.5']) }} role="radiogroup" aria-label="Theme">
    @foreach ($themeOptions as $themeValue => [$themeLabel, $themeIcon])
        <button type="button" role="radio" title="{{ $themeLabel }} theme"
            x-on:click="select('{{ $themeValue }}')"
            x-bind:aria-checked="theme === '{{ $themeValue }}'"
            x-bind:class="theme === '{{ $themeValue }}' ? 'bg-surface text-fg shadow-sm' : 'text-fg-subtle hover:text-fg'"
            class="flex flex-1 items-center justify-center gap-1.5 rounded-md px-2 py-1.5 text-xs font-medium transition-colors">
            <x-icon :name="$themeIcon" class="size-3.5" />
            <span>{{ $themeLabel }}</span>
        </button>
    @endforeach
</div>
