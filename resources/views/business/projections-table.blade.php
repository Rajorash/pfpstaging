<x-ui.table-table class="relative"
                  id="projection_table"
                  data-prev-page="{{$prevPage}}"
                  data-prev-page-title="{{$prevPageTitle}}"
                  data-next-page="{{$nextPage}}"
                  data-next-page-title="{{$nextPageTitle}}"
>
    <thead class="">
    <tr class="border-b divide-x border-light_blue">
        <x-ui.table-th class="sticky top-0 left-0 z-30 text-center"
                       baseClass="w-24 min-w-24 text-dark_gray font-normal bg-white"></x-ui.table-th>
        @foreach($dates as $date)
            <x-ui.table-th
                class="text-center sticky top-0 {{ Carbon\Carbon::parse($date)->isToday() ? 'text-blue': 'text-dark_gray' }}"
                baseClass="w-24 min-w-24 font-normal bg-white">
                    <span
                        class="block text-xs font-normal">{{Carbon\Carbon::parse($date)->format('M Y')}}</span>
                <span class="block text-xl">{{Carbon\Carbon::parse($date)->format('j')}}</span>
            </x-ui.table-th>
        @endforeach
    </tr>
    </thead>
    <x-ui.table-tbody>
        @foreach($allocations as $allocation)
            @php
                $bg = "bg-{$allocation['account']->type}";
            @endphp
            <tr class="hover:bg-yellow-100 border-light_blue divide-x {{$loop->odd ? 'bg-indigo-100' : 'bg-white' }}">
                <x-ui.table-td padding="p-1 pr-2 pl-4"
                               baseClass="text-white {{$bg}}"
                               class="text-left sticky-column sticky left-0 {{$loop->odd ? 'bg-indigo-100' : 'bg-white' }}">
                    {{ $allocation['account']->name }}
                </x-ui.table-td>

                @foreach($dates as $date)
                    @php
                        $value = $allocation['dates']->has($date)
                                 ? $allocation['dates'][$date]->amount
                                 : $allocation['last_val'];
                        $isNegative = $value < 0;
                    @endphp
                    <x-ui.table-td padding="p-0" class="text-right">
                        <input class="percentage-value
                                    border-0 border-transparent bg-transparent
                                    focus:outline-none focus:ring-1 focus:shadow-none focus:bg-white
                                    m-0 outline-none postreal text-right w-full {{$isNegative ? "text-red-500" : ""}}"
                               placeholder=0
                               type="text"
                               value="{{number_format($value, 0)}}"
                               disabled
                        >
                    </x-ui.table-td>
                @endforeach

            </tr>
        @endforeach
    </x-ui.table-tbody>

</x-ui.table-table>
