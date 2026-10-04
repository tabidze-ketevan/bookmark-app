<x-app-layout title="Profile">
    <x-page-header title="Profile" icon="user" />

    <div class="max-w-2xl space-y-6">
        <div class="panel p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="panel p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="panel border-red-500/20 p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
