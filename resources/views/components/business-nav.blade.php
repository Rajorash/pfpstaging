<div class="ml-auto sm:px-4 secondary-nav flex flex-wrap list-none pl-0 mb-0 mt-n3 mb-2">
    @foreach ($links as $link => $label)

    @if ($loop->first)
    <a class="text-blue hover:text-dark_gray2 hover:underline ml-auto mr-2" href="{{$link}}">{{$label}}</a> |
    @elseif (!$loop->last)
    <a class="text-blue hover:text-dark_gray2 hover:underline mx-2" href="{{$link}}">{{$label}}</a> |
    @else
    <a class="text-blue hover:text-dark_gray2 hover:underline ml-2" href="{{$link}}">{{$label}}</a>
    @endif

    @endforeach
</div>
