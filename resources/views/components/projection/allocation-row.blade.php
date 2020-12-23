@props([ 'allocation', 'dates' ])

@php
    $account = $allocation['account'];
    $allocated_dates = $allocation['dates'];
@endphp

<tr>
    <td class="px-3 form-label">{{ $account->name }}</td>
    @foreach($dates as $date)
    @if ( $allocated_dates->has($date) )
    <td>
        <input class="text-right form-control form-control-sm" type="text" placeholder=0 value="{{$allocated_dates[$date]->amount}}" disabled>
    </td>
    @else
    <td>
        <input class="text-right form-control form-control-sm" type="text" placeholder=0 disabled>
    </td>
    @endif
    @endforeach
</tr>
