<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary justify-center focus:ring-2 focus:ring-accent/40 focus:outline-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
