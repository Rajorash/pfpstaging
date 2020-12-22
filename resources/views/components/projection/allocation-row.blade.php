@props([ 'allocation', 'dates' ])

@php
    $account = $allocation['account'];
    $allocated_dates = $allocation['dates'];
@endphp

<tr>
    <td class="px-3">{{ $account->name }}</td>
    @foreach($dates as $date)
    @if ( $allocated_dates->has($date) )
    <td class="text-right">{{$allocated_dates[$date]->amount}}</td>
    @else
    <td class="text-right">0</td>
    @endif
    @endforeach
</tr>
