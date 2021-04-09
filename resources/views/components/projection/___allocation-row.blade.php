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
                <input
                    class="text-right block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded py-1 px-2 text-sm leading-normal rounded"
                    type="text" placeholder=0 value="{{$allocated_dates[$date]->amount}}" disabled>
            </td>
        @else
            <td>
                <input
                    class="text-right block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded py-1 px-2 text-sm leading-normal rounded"
                    type="text" placeholder=0 disabled>
            </td>
        @endif
    @endforeach
</tr>
