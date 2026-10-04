@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="mt-6 flex items-center justify-between gap-3 text-sm" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="rounded-md px-3 py-1.5 text-fg-faint">&larr; Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-md px-3 py-1.5 text-fg-soft hover:bg-hover">&larr; Previous</a>
        @endif

        <span class="text-fg-subtle">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-md px-3 py-1.5 text-fg-soft hover:bg-hover">Next &rarr;</a>
        @else
            <span class="rounded-md px-3 py-1.5 text-fg-faint">Next &rarr;</span>
        @endif
    </nav>
@endif
