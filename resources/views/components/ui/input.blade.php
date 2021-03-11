@props([
    'name' => null, 'id' => null, 'type' => 'text', 'disabled' => false, 'value' => null
])


<input type="{{$type}}"
    {{ $attributes->merge(['class' => 'text-right w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 text-sm leading-normal rounded h-6'])}}
    @if ($value != null)
    value="{{$value}}"
    @endif
    @if ($name != null)
       name="{{$name}}"
    @endif
    @if ($id != null)
        id="{{$id}}"
    @endif
    @if ($disabled) disabled @endif
    >
