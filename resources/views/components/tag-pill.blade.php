@props(['tag', 'href' => null])

@php
    /** Full class strings per color so Tailwind can detect them. Keep keys in sync with Tag::COLORS. */
    $colorClasses = [
        'blue' => 'bg-blue-500/10 text-blue-700 ring-blue-500/20 hover:bg-blue-500/20 dark:bg-blue-400/10 dark:text-blue-300 dark:ring-blue-400/20',
        'violet' => 'bg-violet-500/10 text-violet-700 ring-violet-500/20 hover:bg-violet-500/20 dark:bg-violet-400/10 dark:text-violet-300 dark:ring-violet-400/20',
        'emerald' => 'bg-emerald-500/10 text-emerald-700 ring-emerald-500/20 hover:bg-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20',
        'amber' => 'bg-amber-500/10 text-amber-700 ring-amber-500/25 hover:bg-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20',
        'rose' => 'bg-rose-500/10 text-rose-700 ring-rose-500/20 hover:bg-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20',
        'cyan' => 'bg-cyan-500/10 text-cyan-700 ring-cyan-500/25 hover:bg-cyan-500/20 dark:bg-cyan-400/10 dark:text-cyan-300 dark:ring-cyan-400/20',
        'fuchsia' => 'bg-fuchsia-500/10 text-fuchsia-700 ring-fuchsia-500/20 hover:bg-fuchsia-500/20 dark:bg-fuchsia-400/10 dark:text-fuchsia-300 dark:ring-fuchsia-400/20',
        'lime' => 'bg-lime-500/15 text-lime-800 ring-lime-600/25 hover:bg-lime-500/25 dark:bg-lime-400/10 dark:text-lime-300 dark:ring-lime-400/20',
    ];
    $pillClasses = 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset transition-colors '.$colorClasses[$tag->color()];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($pillClasses) }}>#{{ $tag->title }}</a>
@else
    <span {{ $attributes->class($pillClasses) }}>#{{ $tag->title }}</span>
@endif
