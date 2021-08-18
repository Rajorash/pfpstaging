@props(['active'])

@php
    $classes = ($active ?? false)
                ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 font-medium leading-5 text-blue focus:outline-none focus:border-blue transition duration-150 ease-in-out'
                : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-blue focus:outline-none focus:text-gray-700 focus:blue transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
