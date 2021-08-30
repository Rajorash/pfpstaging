<div class="flex flex-wrap flex-shrink-0 mt-8 ml-auto secondary-nav">

    @if(Auth::user()->isAdvisor()
        &&
        (is_object($business->collaboration)
            && is_object($business->collaboration->advisor)
            && $business->collaboration->advisor->user_id  != auth()->user()->id)
            || !is_object($business->collaboration)
        )
        @php
            $active = request()->is('*business/*/maintenance');
        @endphp
        <a href="{{route('maintenance.business', ['business' => $business])}}" title="{{__('Maintenance')}}"
           class="bg-white block rounded box-border p-3 flex mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
               ">
            <x-icons.gear :class="'h-4 w-auto my-0.5 inline-block'"/>
            <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">{{__('Maintenance')}}</span>
        </a>
    @endif

    @php
        $active = request()->is('*business/*/accounts');
    @endphp
    <a href="{{url('/business/'.$businessId.'/accounts')}}" title="{{__('Accounts')}}"
       class="bg-white block rounded box-border p-3 flex mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.vallet :class="'h-6 w-auto inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">{{__('Accounts')}}</span>
    </a>

    @php
        $active = request()->routeIs('allocations-percentages');
    @endphp
    <a href="{{route('allocations-percentages', ['business' => $business])}}" title="{{__('Rollout Percentages')}}"
       class="bg-white block rounded box-border p-3 flex text-gray-700 mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.percent :class="'h-5 w-auto mt-1 inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">{{__('Percentages')}}</span>
    </a>

    @php
        $active = request()->routeIs('allocation-calculator-with-id');
    @endphp
    <a href="{{route('allocation-calculator-with-id', ['business' => $business])}}" title="{{__('Allocation Calculator')}}"
       class="bg-white block rounded box-border p-3 flex text-gray-700 mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.calculator :class="'h-6 w-auto inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">{{__('Calculator')}}</span>
    </a>

    @php
        $active = request()->routeIs('allocations-calendar');
    @endphp
    <a href="{{route('allocations-calendar', ['business' => $business])}}" title="{{__('Projection Data Entry')}}"
       class="bg-white block rounded box-border p-3 flex text-gray-700 mr-6 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.table :class="'h-6 w-auto inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">{{__('Data Entry')}}</span>
    </a>

    @php
        $active = request()->routeIs('projections');
    @endphp
    <a href="{{route('projections', ['business' => $business])}}" title="{{__('Projection Forecast')}}"
       class="bg-white block rounded box-border p-3 flex text-gray-700 h-12
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.presentation-chart :class="'h-5 w-auto mt-1 inline-block'"/>
        <span class="ml-2 text-lg inline-block @if($active) text-blue @else hidden @endif">{{__('Projection Forecast')}}</span>
    </a>
</div>
