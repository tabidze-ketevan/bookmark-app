<x-layout title="Edit bookmark">
    <x-page-header title="Edit bookmark" icon="pencil" />

    <form method="POST" action="{{ route('bookmarks.update', $bookmark) }}" class="panel max-w-2xl p-6">
        @method('PUT')
        @include('bookmarks._form')
    </form>
</x-layout>
