@props(['bookmark', 'size' => 'sm'])

@php
    $domain = $bookmark->domain;
    $letter = strtoupper(substr($domain ?: (string) $bookmark->title, 0, 1)) ?: '?';
    /** Small tiles use a stronger tint; large grid thumbnails stay soft so the favicon stands out. */
    $palettes = [
        'sm' => [
            'bg-blue-500/15 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            'bg-violet-500/15 text-violet-700 dark:bg-violet-500/20 dark:text-violet-300',
            'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
            'bg-amber-500/15 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
            'bg-rose-500/15 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
            'bg-cyan-500/15 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300',
        ],
        'lg' => [
            'bg-blue-500/[0.06] text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
            'bg-violet-500/[0.06] text-violet-700 dark:bg-violet-500/15 dark:text-violet-300',
            'bg-emerald-500/[0.06] text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
            'bg-amber-500/[0.07] text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
            'bg-rose-500/[0.06] text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
            'bg-cyan-500/[0.07] text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-300',
        ],
    ];
    $palette = $palettes[$size] ?? $palettes['sm'];
    $tone = $palette[crc32($domain ?: (string) $bookmark->title) % count($palette)];
@endphp

<div {{ $attributes->class([
    'relative grid shrink-0 place-items-center overflow-hidden font-semibold',
    $tone,
    'size-10 rounded-md text-sm' => $size === 'sm',
    'aspect-[16/9] w-full rounded-t-lg text-3xl' => $size === 'lg',
]) }}>
    <span>{{ $letter }}</span>
    @if ($domain)
        <img src="https://www.google.com/s2/favicons?domain={{ urlencode($domain) }}&sz={{ $size === 'lg' ? 128 : 64 }}" alt="" loading="lazy"
            onerror="this.remove()"
            @class(['absolute inset-0 m-auto bg-surface object-contain', 'size-full p-2.5' => $size === 'sm', 'size-16 rounded-xl p-2 shadow-lg shadow-black/30' => $size === 'lg'])>
    @endif
</div>
