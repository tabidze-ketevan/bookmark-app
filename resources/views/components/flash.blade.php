@if (session('status'))
    <div class="mb-5 flex items-center gap-2 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-300" role="status">
        <x-icon name="check" />
        {{ session('status') }}
    </div>
@endif
