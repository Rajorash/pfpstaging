@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-normal text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
