{{-- Quick actions revealed on hover (always visible on touch-sized screens). --}}
<div class="relative z-10 flex shrink-0 items-center gap-0.5 transition-opacity focus-within:opacity-100 group-hover:opacity-100 pointer-fine:opacity-0">
    <button type="button" x-data="copyUrl(@js($bookmark->url))" x-on:click="copy" title="Copy URL"
        x-bind:title="copied ? 'Copied!' : 'Copy URL'"
        class="relative inline-flex items-center rounded-md p-1.5 text-fg-muted hover:bg-hover hover:text-fg">
        <x-icon name="clipboard" x-show="! copied" />
        <x-icon name="check" x-show="copied" x-cloak class="size-4 text-emerald-600 dark:text-emerald-400" />
        <span x-show="copied" x-cloak x-transition.opacity
            class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded-md bg-fg px-2 py-1 text-xs font-medium whitespace-nowrap text-canvas shadow-lg"
            role="status">Copied!</span>
        <span class="sr-only">Copy URL</span>
    </button>
    <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" title="Open link" class="rounded-md p-1.5 text-fg-muted hover:bg-hover hover:text-fg">
        <x-icon name="external" />
        <span class="sr-only">Open link</span>
    </a>
    <a href="{{ route('bookmarks.edit', $bookmark) }}" title="Edit" class="rounded-md p-1.5 text-fg-muted hover:bg-hover hover:text-fg">
        <x-icon name="pencil" />
        <span class="sr-only">Edit</span>
    </a>
    <x-delete-button :action="route('bookmarks.destroy', $bookmark)" confirm="Delete this bookmark?" />
</div>
