@props([
    'name' => null, 'id' => null, 'type' => 'text', 'disabled' => false, 'value' => null
])


<input type="{{$type}}"
    {{ $attributes->merge(['class' => 'form-input text-right w-full p-2 mb-1 text-base leading-normal border-gray-200 rounded h-6'])}}
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
