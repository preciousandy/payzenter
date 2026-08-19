@props(['value'])

<label {{ $attributes->merge(['class' => 'input-lablel']) }}>
    {{ $value ?? $slot }}
</label>