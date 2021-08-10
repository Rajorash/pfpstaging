<div class="flex flex-wrap flex-shrink-0 mt-8 ml-auto secondary-nav">

    @php
        $active = request()->is('*business/*/accounts');
    @endphp
    <a href="{{url('/business/'.$businessId.'/accounts')}}" title="Accounts"
       class="bg-white block rounded box-border p-3 flex mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.vallet :class="'h-6 w-auto inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">Accounts</span>
    </a>

    @php
        $active = request()->routeIs('allocations-percentages');
    @endphp
    <a href="{{route('allocations-percentages', ['business' => $business])}}" title="Rollout Percentages"
       class="bg-white block rounded box-border p-3 flex text-gray-700 mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.percent :class="'h-5 w-auto mt-1 inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">Percentages</span>
    </a>

    @php
        $active = request()->routeIs('allocation-calculator-with-id');
    @endphp
    <a href="{{route('allocation-calculator-with-id', ['business' => $business])}}" title="Allocation Calculator"
       class="bg-white block rounded box-border p-3 flex text-gray-700 mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.calculator :class="'h-6 w-auto inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">Calculator</span>
    </a>

    @php
        $active = request()->routeIs('allocations-calendar');
    @endphp
    <a href="{{route('allocations-calendar', ['business' => $business])}}" title="Projection Data Entry"
       class="bg-white block rounded box-border p-3 flex text-gray-700 mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.table :class="'h-6 w-auto inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">Data Entry</span>
    </a>

    @php
        $active = request()->routeIs('projections');
    @endphp
    <a href="{{route('projections', ['business' => $business])}}" title="Projection Forecast"
       class="bg-white block rounded box-border p-3 flex text-gray-700 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.presentation-chart :class="'h-5 w-auto mt-1 inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">Projection Forecast</span>
    </a>


    {{--    @foreach ($links as $link => $labelData)--}}

    {{--        <a class="text-blue hover:text-dark_gray2 hover:underline--}}
    {{--            @if ($loop->first)--}}
    {{--            ml-auto mr-2--}}
    {{--            @elseif (!$loop->last)--}}
    {{--            mx-2--}}
    {{--            @else--}}
    {{--            ml-2--}}
    {{--            @endif--}}
    {{--        @if($labelData['active'])--}}
    {{--            text-dark_gray2 underline--}}
    {{--            @endif--}}
    {{--            " href="{{$link}}">--}}

    {{--            {{$labelData['title']}}--}}
    {{--        </a>--}}

    {{--        @if ($loop->first)--}}
    {{--            |--}}
    {{--        @elseif (!$loop->last)--}}
    {{--            |--}}
    {{--        @else--}}

    {{--        @endif--}}

    {{--    @endforeach--}}
</div>
