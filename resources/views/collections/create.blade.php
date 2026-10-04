<x-layout title="New collection">
    <x-page-header title="New collection" icon="folder" />

    <form method="POST" action="{{ route('collections.store') }}" class="panel max-w-xl p-6">
        @include('collections._form')
    </form>
</x-layout>
