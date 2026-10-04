<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-edge px-4 py-2 text-sm font-medium text-fg-soft hover:bg-hover focus:ring-2 focus:ring-accent/40 focus:outline-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
