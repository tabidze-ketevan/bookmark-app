@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-fg-soft']) }}>
    {{ $value ?? $slot }}
</label>
