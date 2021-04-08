@props([
'class' => 'pt-12 pb-6 px-12'
])
<caption class="{{$class}}">
    <span class="flex justify-between">

        @if(isset($left))
            {{$left}}
        @endif

        <span class="text-2xl text-dark_gray2 text-left">
            {{$slot}}
        </span>

        @if(isset($right))
            {{$right}}
        @endif

    </span>
</caption>
