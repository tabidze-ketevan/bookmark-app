<x-layout title="Edit collection">
    <x-page-header title="Edit collection" icon="pencil" />

    <form method="POST" action="{{ route('collections.update', $collection) }}" class="panel max-w-xl p-6">
        @method('PUT')
        @include('collections._form')
    </form>
</x-layout>
