@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-black-500']) }}>
    {{ $value ?? $slot }}
</label>
