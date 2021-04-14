@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-normal text-sm text-dark_gray']) }}>
    {{ $value ?? $slot }}
</label>
