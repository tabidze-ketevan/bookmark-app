<x-layout title="Edit tag">
    <x-page-header title="Edit tag" icon="pencil" />

    <form method="POST" action="{{ route('tags.update', $tag) }}" class="panel max-w-xl p-6">
        @method('PUT')
        @include('tags._form')
    </form>
</x-layout>
