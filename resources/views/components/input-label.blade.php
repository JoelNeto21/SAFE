@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-zinc-800']) }}>
    {{ $value ?? $slot }}
</label>
