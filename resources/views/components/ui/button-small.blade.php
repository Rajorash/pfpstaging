@props([
'href' => '#',
'type' => 'a',
'background' => 'bg-blue hover:bg-dark_gray2',
'padding' => 'py-1 px-3',
'attr' => ''
])

@if($type == 'a')
    <a href="{{$href}}" {{$attr}}
    {!! $attributes->merge(['class' => 'inline-block align-middle text-center select-none border font-normal
                whitespace-no-wrap rounded-lg no-underline text-white flex items-center
                leading-tight text-xs '.$background.' '.$padding]) !!}
@elseif($type=='button')
    <button {{$attr}}
        {!! $attributes->merge(['class' => 'text-center select-none border font-normal
                whitespace-no-wrap rounded-lg no-underline text-white flex items-center
                py-1 px-3 leading-tight text-xs '.$background]) !!}
        @endif
    >
        {{$slot}}
        @if($type == 'a')
        </a>
        @elseif($type=='button')
    </button>
@endif
