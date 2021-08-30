@props([
'href' => '#',
'type' => 'a'
])
@if($type == 'a')
    <a href="{{$href}}"
    {!! $attributes->merge(['class' => 'group inline-flex items-center text-center select-none border font-normal whitespace-no-wrap
           rounded-lg py-1 px-3 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2']) !!}
@elseif($type=='button')
    <button
        {!! $attributes->merge(['class' => 'text-center select-none border font-normal whitespace-no-wrap
           rounded-lg py-2 px-6 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2']) !!}
        @endif
    >
        {{$slot}}
        @if($type == 'a')
        </a>
        @elseif($type=='button')
    </button>
@endif
