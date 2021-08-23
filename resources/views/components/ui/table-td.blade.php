@props([
'baseClass' => 'text-dark_gray2',
'border' => '',
'class' => 'text-left',
'padding' => 'px-2 py-4',
'attr' => ''
])
<td class="{{$border}} {{$padding}} {{$baseClass}} {{$class}}" {{$attr}} >
    {{$slot}}
</td>
