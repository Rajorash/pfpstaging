@props(['acc', 'dates', 'row' => $row, 'first', 'type' => $acc->type])
<tr>
    <td class="px-2 py-1 whitespace-nowrap bg-blue-300">
        @if ($first)
        <div class="h-8 text-gray-500 font-semibold tracking-tight">{{ucfirst($type)}} Accounts</div>
        @endif
        <div class="mb-1 font-semibold">{{ $acc->name }}</div>
        @if ($type != 'revenue')
        <div class="mb-1 text-gray-500">Transfer In</div>
        <div class="text-gray-500">Flow Total</div>
        @endif
    </td>
    @foreach ($dates as $date)
    <td class="p-1 whitespace-nowrap bg-blue-300"
        data-date='{{$date}}'
        data-hierarchy="{{$type}}"
        {{-- data-phase='{{$phaseDates[$date]}}' --}}
        {{-- data-percentage='{{$allocationPercentages[$phaseDates[$date]][$acc->id]??0}}' --}}
        data-row='{{$row}}'
        data-col='{{$loop->iteration}}'
    >
        @if ($first)
        <div class="h-8"></div>
        @endif

        @unless ($acc->type == 'revenue')
        <x-ui.input class="block text-right" placeholder=0 />
        <x-ui.input class="block text-right bg-blue-100" placeholder=0 disabled="true" />
        @endunless
        <x-ui.input class="block text-right bg-yellow-100" placeholder=0 disabled="true" />
    </td>
    @endforeach
</tr>
