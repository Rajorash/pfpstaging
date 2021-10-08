@props([ 'business', 'step'])

@php
    $prev = $next = '#';
    switch ($step){
        case 'allocation-calculator':
            $prev = '';
            $next = route('allocations-calendar', ['business' => $business]);
            break;
        case 'allocations':
            $prev = route('allocation-calculator-with-id', ['business' => $business]);
            $next = url('/business/'.$business->id.'/balance');
            break;
        case 'balance':
            $prev = route('allocations-calendar', ['business' => $business]);
            $next = route('projections', ['business' => $business]);
            break;
        case 'projections':
            $prev = url('/business/'.$business->id.'/balance');
            $next = route('allocations-percentages', ['business' => $business]);
            break;
        case 'percentages':
            $prev = route('projections', ['business' => $business]);
            $next = '';
            break;
    }
@endphp
<div class="absolute right-0 top-0 w-full z-50">
    @if($prev)
        <a href="{{$prev}}"
           class="opacity-20 hover:opacity-100 float-left bg-blue hover:bg-dark_gray text-white px-4 py-3 rounded-br">
            <x-icons.chevron-left :class="'h-3 w-auto inline-block align-middle mr-1'"/>
            <span class="inline-block align-middle">prev</span>
        </a>
    @endif
    @if($next)
        <a href="{{$next}}"
           class="opacity-20 hover:opacity-100 float-right bg-blue hover:bg-dark_gray text-white px-4 py-3 rounded-bl">
            <span class="inline-block align-middle">next</span>
            <x-icons.chevron-right :class="'h-3 w-auto inline-block align-middle ml-1'"/>
        </a>
    @endif
</div>
