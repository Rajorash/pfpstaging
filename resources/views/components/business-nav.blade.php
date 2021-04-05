<div class="ml-auto secondary-nav flex flex-wrap mt-8">

    @foreach ($links as $link => $labelData)

        <a class="text-blue hover:text-dark_gray2 hover:underline
            @if ($loop->first)
            ml-auto mr-2
            @elseif (!$loop->last)
            mx-2
            @else
            ml-2
            @endif
            @if($labelData['active'])
            text-dark_gray2 underline
            @endif
            " href="{{$link}}">{{$labelData['title']}}</a>

        @if ($loop->first)
            |
        @elseif (!$loop->last)
            |
        @else

        @endif

    @endforeach
</div>
