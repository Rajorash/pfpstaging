@props(['acc', 'dates', 'row' => $row, 'first', 'type'])
<tr>
    <td class="px-2 py-1 whitespace-nowrap bg-blue-300 sticky left-0">
        @if ($first)
            <div class="h-8 text-gray-500 font-semibold tracking-tight">{{ucfirst($acc->type)}} Accounts</div>
        @endif
        <div class="mb-1 font-semibold">{{ $acc->name }}</div>
        @if ($acc->type != 'revenue')
            <div class="mb-1 text-gray-500">Transfer In</div>
            <div class="text-gray-500">Flow Total</div>
        @endif
    </td>
    @foreach ($dates as $date)
    <td class="p-1 whitespace-nowrap bg-blue-300"
        data-date='{{$date}}'
        data-hierarchy="{{$acc->type}}"
        {{-- data-phase='{{$phaseDates[$date]}}' --}}
        {{-- data-percentage='{{$allocationPercentages[$phaseDates[$date]][$acc->id]??0}}' --}}
        data-row='{{$row}}'
        data-col='{{$loop->iteration}}'
    >
        @if ($first)
        <div class="h-8"></div>
        @endif

        @unless ($acc->type == 'revenue')
            {{-- <x-ui.input class="block text-right" placeholder=0 /> --}}
            {{-- Account Total --}}
            <livewire:calculator.account-value accountId="{{$acc->id}}" :date="$date" key="value_{{$acc->id}}-{{$date->format('Y-m-d')}}"/>
            {{-- Transfer In --}}
            <livewire:calculator.account-transfer accountId="{{$acc->id}}" :date="$date" key="transfer_{{$acc->id}}-{{$date->format('Y-m-d')}}"/>
        @endunless

        <livewire:calculator.account-total accountId="{{$acc->id}}" :date="$date" key="total_{{$acc->id}}-{{$date->format('Y-m-d')}}"/>
     </td>
    @endforeach
</tr>
