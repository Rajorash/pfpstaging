@props(['route'])

@php
    $classes = Request::routeIs($route) ? 'text-success' : 'text-dark';
@endphp

<li class="nav-item">
    <a href="{{ route($route) }}"
        {{ $attributes->merge(['class' => "nav-link px-3 " . $classes]) }}
    >
        {{ $slot }}
    </a>
</li>
