<x-app-layout>
    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Projections
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>


    <x-ui.main width="w-full">
        <x-ui.table-table>
            <thead>
            <tr class="border-light_blue divide-x border-b">
                <x-ui.table-th class="text-center" baseClass="w-24 text-dark_gray font-normal"></x-ui.table-th>
                @foreach($dates as $date)
                    <x-ui.table-th class="text-center" baseClass="w-24 text-dark_gray font-normal"
                                   class="{{ Carbon\Carbon::parse($date)->isToday() ? 'text-blue': '' }} ">
                    <span
                        class="block text-xs font-normal">{{Carbon\Carbon::parse($date)->format('M Y')}}</span>
                        <span class="block text-xl">{{Carbon\Carbon::parse($date)->format('j')}}</span>
                    </x-ui.table-th>
                @endforeach
            </tr>
            </thead>
            <x-ui.table-tbody>
                @foreach($allocations as $allocation)
                    <tr class="hover:bg-yellow-100 border-light_blue divide-x {{$loop->odd ? 'bg-indigo-100' : '' }}">
                        <x-ui.table-td padding="p-1 pr-2 pl-4">
                            {{ $allocation['account']->name }}
                        </x-ui.table-td>

                        @foreach($dates as $date)
                            <x-ui.table-td padding="p-0" class="text-right">
                                <input class="percentage-value
                                    border-0 border-transparent bg-transparent
                                    focus:outline-none focus:ring-1 focus:shadow-none focus:bg-white
                                    m-0 outline-none postreal text-right w-full"
                                       placeholder=0
                                       type="text"
                                       @if ( $allocation['dates']->has($date) )
                                       value="{{number_format($allocation['dates'][$date]->amount, 0)}}"
                                       @endif
                                       disabled
                                >
                            </x-ui.table-td>
                        @endforeach

                    </tr>
                @endforeach
            </x-ui.table-tbody>

        </x-ui.table-table>
    </x-ui.main>
</x-app-layout>
