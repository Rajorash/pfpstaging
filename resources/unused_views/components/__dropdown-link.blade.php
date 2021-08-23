@props(['route'])

@php
    $classes = Request::routeIs($route) ? 'text-success' : 'text-dark';
@endphp

<a href="{{ route($route) }}"
    {{ $attributes->merge(['class' => "dropdown-item " . $classes]) }}
>
    {{ $slot }}
</a>
