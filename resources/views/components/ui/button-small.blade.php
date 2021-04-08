@props([
'href' => '#'
])
<a {!! $attributes->merge(['class' => 'inline-block align-middle text-center select-none border font-normal
                whitespace-no-wrap rounded-lg no-underline bg-blue text-white
                hover:bg-dark_gray2 py-1 px-3 leading-tight text-xs']) !!}
   href="{{$href}}">
    {{$slot}}
</a>
