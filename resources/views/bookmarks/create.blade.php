<x-layout title="Add bookmark">
    <x-page-header title="Add bookmark" icon="plus" />

    <form method="POST" action="{{ route('bookmarks.store') }}" class="panel max-w-2xl p-6">
        @include('bookmarks._form')
    </form>
</x-layout>
