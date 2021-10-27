@props([ 'business', 'step'])

@php
    $prev = $next = '#';
    $prevText = $nextText = '';
    switch ($step){
        case 'allocation-calculator':
            $prev = '';
            $prevText = '';
            $next = url('/business/'.$business->id.'/balance');
            $nextText = 'Change balance';
            break;
        case 'balance':
            $prev = route('allocation-calculator-with-id', ['business' => $business]);
            $prevText = 'Allocations Calculator';
            $next = url('/business/'.$business->id.'/revenue-entry');
            $nextText = 'Revenue Entry';
            break;
        case 'revenue':
            $prev = url('/business/'.$business->id.'/balance');
            $prevText = 'Change balance';
            $next = route('allocations-calendar', ['business' => $business]);
            $nextText = 'Expense Entry';
            break;
        case 'allocations':
            $prev = url('/business/'.$business->id.'/revenue-entry');
            $prevText = 'Revenue Entry';
            $next = route('projections', ['business' => $business]);
            $nextText = 'Projection Forecast';
            break;
        case 'projections':
            $prev = route('allocation-calculator-with-id', ['business' => $business]);
            $prevText = 'Allocations Calculator';
            $next = route('allocations-percentages', ['business' => $business]);
            $nextText = 'Percentages';
            break;
        case 'percentages':
            $prev = route('projections', ['business' => $business]);
            $prevText = 'Projection Forecast';
            $next = '';
            $nextText = '';
            break;
    }
@endphp
<div class="absolute top-0 right-0 z-50 w-full px-4 sm:px-6 lg:px-8">
    @if($prev)
        <a href="{{$prev}}" title="@if($prevText) Back to {{$prevText}} @endif"
           class="float-left px-4 py-1 text-base text-white rounded-bl rounded-br opacity-70 hover:opacity-100 bg-blue hover:bg-dark_gray">
            <x-icons.chevron-left :class="'h-3 w-auto inline-block align-middle mr-1'"/>
            <span class="inline-block align-middle">@if($prevText) {{$prevText}} @else prev @endif</span>
        </a>
    @endif
    @if($next)
        <a href="{{$next}}" title="@if($nextText) Go to {{$nextText}} @endif"
           class="float-right px-4 py-1 text-base text-white rounded-bl rounded-br opacity-70 hover:opacity-100 bg-blue hover:bg-dark_gray">
            <span class="inline-block align-middle">@if($nextText) {{$nextText}} @else next @endif</span>
            <x-icons.chevron-right :class="'h-3 w-auto inline-block align-middle ml-1'"/>
        </a>
    @endif
</div>
