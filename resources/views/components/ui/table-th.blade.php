@props([
'baseClass' => 'bg-gray-100 text-dark_gray uppercase text-xs leading-none',
'border' => '',
'class' => 'text-left',
'padding' => 'px-2 py-4'
])
<th class="{{$border}} {{$padding}} {{$baseClass}} {{$class}}">
    {{$slot}}
</th>
