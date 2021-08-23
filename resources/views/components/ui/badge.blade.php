@props([
'background' => 'bg-green'
])
<span class="inline-flex text-xs rounded-full py-0.5 px-2 leading-tight text-white {{$background}}">
{{$slot}}
</span>
