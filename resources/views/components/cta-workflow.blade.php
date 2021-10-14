@props([ 'business', 'step'])

@php
    $prev = $next = '#';
    $prevText = $nextText = '';
    switch ($step){
        case 'allocation-calculator':
            $prev = '';
            $prevText = '';
            $next = route('allocations-calendar', ['business' => $business]);
            $nextText = 'Allocations';
            break;
        case 'allocations':
            $prev = route('allocation-calculator-with-id', ['business' => $business]);
            $prevText = 'Allocations Calculator';
            $next = url('/business/'.$business->id.'/balance');
            $nextText = 'Change balance';
            break;
        case 'balance':
            $prev = route('allocations-calendar', ['business' => $business]);
            $prevText = 'Allocations';
            $next = route('projections', ['business' => $business]);
            $nextText = 'Projection Forecast';
            break;
        case 'projections':
            $prev = url('/business/'.$business->id.'/balance');
            $prevText = 'Change balance';
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
<div class="absolute right-0 top-0 w-full z-50 px-4 sm:px-6 lg:px-8">
    @if($prev)
        <a href="{{$prev}}" title="@if($prevText) Back to {{$prevText}} @endif"
           class="opacity-70 hover:opacity-100 float-left bg-blue hover:bg-dark_gray text-white px-4 py-1 rounded-br rounded-bl text-base">
            <x-icons.chevron-left :class="'h-3 w-auto inline-block align-middle mr-1'"/>
            <span class="inline-block align-middle">@if($prevText) {{$prevText}} @else prev @endif</span>
        </a>
    @endif
    @if($next)
        <a href="{{$next}}" title="@if($nextText) Go to {{$nextText}} @endif"
           class="opacity-70 hover:opacity-100 float-right bg-blue hover:bg-dark_gray text-white px-4 py-1 rounded-br rounded-bl text-base">
            <span class="inline-block align-middle">@if($nextText) {{$nextText}} @else next @endif</span>
            <x-icons.chevron-right :class="'h-3 w-auto inline-block align-middle ml-1'"/>
        </a>
    @endif
</div>
