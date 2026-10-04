@props(['tag'])

@php
    /** Keep keys in sync with Tag::COLORS. */
    $dotClasses = [
        'blue' => 'bg-blue-500', 'violet' => 'bg-violet-500', 'emerald' => 'bg-emerald-500', 'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500', 'cyan' => 'bg-cyan-500', 'fuchsia' => 'bg-fuchsia-500', 'lime' => 'bg-lime-500',
    ];
@endphp

<span {{ $attributes->class(['size-2 shrink-0 rounded-full', $dotClasses[$tag->color()]]) }} aria-hidden="true"></span>
