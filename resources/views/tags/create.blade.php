<x-layout title="New tag">
    <x-page-header title="New tag" icon="hashtag" />

    <form method="POST" action="{{ route('tags.store') }}" class="panel max-w-xl p-6">
        @include('tags._form')
    </form>
</x-layout>
