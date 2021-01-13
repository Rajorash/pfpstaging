<x-app-layout>
    <x-slot name="header">
        <div class="flex content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$business->name}} > Allocations
            </h2>
            <x-business-nav businessId="{{$business->id}}" />
            </div>

        </x-slot>


        <div class="flex flex-wrap ">
            <table id="allocationTable" class="w-full max-w-full mb-4 bg-transparent table-hover p-1 block w-full overflow-auto scrolling-touch">
                <thead class="thead-inverse">
                    <tr>
                        <th></th>
                        @foreach($dates as $date)
                        <th class="text-right">
                            <span style="{{$today->format('Y-m-j') == $date ? 'color: #bada55' : ''}}">{{ Carbon\Carbon::parse($date)->format("M j Y") }}</span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($business->accounts as $acc)
                    <tr class="align-middle">
                        <td class="account-row bg-blue-200" scope="row">
                            <label class="align-middle block px-3 mt-0 mb-2">{{$acc->name}}</label>
                            @unless ($acc->type == 'revenue')
                            <label class="align-middle block px-3 mt-0 mb-2 text-gray-500">Transfer in</label>
                            <label class="align-middle block px-3 mt-0 mb-2 text-gray-500">Daily Total</label>
                            @endunless
                        </td>
                        @foreach($dates as $date)
                        <td class="account text-right py-1 bg-blue-200"
                        data-date='{{$date}}'
                        data-hierarchy="{{$acc->type}}"
                        data-phase='{{$phaseDates[$date]}}'
                        data-percentage='{{$allocationPercentages[$phaseDates[$date]][$acc->id]??0}}'
                        data-row='{{$loop->parent->iteration}}'
                        data-col='{{$loop->iteration}}'>

                        @unless ($acc->type == 'revenue')
                        <input type="text" class="cumulative text-right allocation-value text-bold block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded py-1 px-2 text-sm leading-normal rounded"
                        data-type="BankAccount"
                        data-id="{{$acc->id}}"
                        data-date="{{$date}}"
                        @if ($allocationValues['BankAccount'][$acc->id][$date] ?? false)
                        value="{{$allocationValues['BankAccount'][$acc->id][$date]}}"
                        @endif
                        >

                        <input type="text"
                        class="bg-teal-500 projected-total text-right block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-blue-100 text-gray-800 border border-gray-200 rounded py-1 px-2 text-sm leading-normal rounded"
                        data-hierarchy="{{$acc->type}}"
                        data-date='{{$date}}'
                        placeholder="0"
                        disabled>
                        @endunless

                        <input type="text"
                        class="daily-total bg-yellow-100 text-right block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded py-1 px-2 text-sm leading-normal rounded border-teal-500"
                        style="min-width: 8em;"
                        data-type="BankAccount"
                        data-hierarchy="{{$acc->type}}"
                        data-id="{{$acc->id}}"
                        data-date="{{$date}}"
                        @if ($acc->type == 'revenue')
                        value="{{$allocationValues['BankAccount'][$acc->id][$date] ?? 0}}"
                        @else
                        value="0"
                        @endif

                        disabled>

                    </td>
                    @endforeach
                </tr>
                @forelse ($acc->flows as $flow)
                <tr>
                    <td class="flow-label bg-{{ $flow->negative_flow ? 'red-200' : 'green-200' }}" scope="row">
                        <label class="align-middle px-3 mt-0 mb-2">{{$flow->label}}</label>
                    </td>
                    @foreach($dates as $date)
                    <td class="text-right flow">
                        <input style="min-width: 8em;"
                        class="text-right allocation-value block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded py-1 px-2 text-sm leading-normal rounded"
                        data-type="AccountFlow"
                        data-id="{{$flow->id}}"
                        data-date="{{$date}}"
                        data-parent="{{$acc->id}}"
                        data-direction="{{$flow->negative_flow ? 'negative' : 'positive'}}"
                        placeholder='0'
                        type="text"
                        value="{{$allocationValues['AccountFlow'][$flow->id][$date] ?? ''}}">
                    </td>
                    @endforeach
                </tr>
                @empty
                @endforelse
                @empty
                <tr>
                    <td scope="row">N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
