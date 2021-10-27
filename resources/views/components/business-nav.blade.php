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
           class="busnav-btn mr-2
        @if($active) text-blue @else text-gray-700 @endif
               ">
            <x-icons.gear class="inline-block w-auto h-6"/>
            <span
                class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Maintenance')}}</span>
        </a>
    @endif

    @php
        $active = request()->is('*business/*/accounts');
    @endphp
    <a href="{{url('/business/'.$businessId.'/accounts')}}" title="{{__('Accounts')}}"
       class="busnav-btn mr-2
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.vallet class="inline-block w-auto h-6"/>
        <span class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Accounts')}}</span>
    </a>

    @php
        $active = request()->routeIs('allocations-percentages');
    @endphp
    <a href="{{route('allocations-percentages', ['business' => $business])}}" title="{{__('Rollout Percentages')}}"
       class="busnav-btn mr-8
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.percent class="inline-block w-auto h-6"/>
        <span class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Percentages')}}</span>
    </a>

    @php
        $active = request()->routeIs('allocation-calculator-with-id');
    @endphp
    <a href="{{route('allocation-calculator-with-id', ['business' => $business])}}"
       title="{{__('Allocation Calculator')}}"
       class="busnav-btn mr-2
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.calculator class="inline-block w-auto h-6"/>
        <span class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Calculator')}}</span>
    </a>


        @php
            $active = request()->routeIs('balance.business');
        @endphp
        <a href="{{url('/business/'.$businessId.'/balance')}}" title="{{__('Manually change balances')}}"
           class="busnav-btn mr-2
            @if($active) text-blue @else text-gray-700 @endif
               ">
            <x-icons.balance class="inline-block w-auto h-6"/>
            <span class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Balances')}}</span>
        </a>

    @php
        $active = request()->routeIs('revenue-entry.table');
    @endphp
    <a href="{{route('revenue-entry.table', ['business' => $business])}}" title="{{__('Revenue Entry')}}"
       class="busnav-btn mr-2
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.dollar-fill class="inline-block w-auto h-6"/>
        <span class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Revenue Entry')}}</span>
    </a>

    @php
        $active = request()->routeIs('allocations-calendar');
    @endphp
    <a href="{{route('allocations-calendar', ['business' => $business])}}" title="{{__('Projection Data Entry')}}"
       class="busnav-btn mr-2
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.table class="inline-block w-auto h-6"/>
        <span class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Data Entry')}}</span>
    </a>

    @php
        $active = request()->routeIs('projections');
    @endphp
    <a href="{{route('projections', ['business' => $business])}}" title="{{__('Projection Forecast')}}"
       class="busnav-btn
        @if($active) text-blue @else text-gray-700 @endif
           ">
        <x-icons.presentation-chart class="inline-block w-auto h-6"/>
        <span
            class="ml-2 text-lg inline-block @if(!$active) hidden @endif">{{__('Projection Forecast')}}</span>
    </a>

</div>
