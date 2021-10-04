@if (!$phase)
    <x-ui.error class="p-12 block">{{__('Date is to fare or late. Set another date.')}}</x-ui.error>
@else
    <x-ui.table-table class="cursor-fill-data relative mb-2">
        <thead>
        <tr class="border-light_blue divide-x border-b">
            <x-ui.table-th class="text-center sticky top-0 left-0"
                           baseClass="min-w-24 w-32 text-dark_gray font-normal bg-white z-30">
                <span id="processCounter" class="hidden opacity-50 font-normal text-xs"></span>
            </x-ui.table-th>

            @foreach($period as $date)
                @php
                    $date = Carbon\Carbon::parse($date, 'Y-m-d H:i:s');
                @endphp
                <x-ui.table-th class="text-center {{ $date->isToday() ? 'text-blue': 'text-dark_gray' }} sticky top-0"
                               baseClass="min-w-24 font-normal bg-white z-20">
                    <span class="block text-xs font-normal">{{$date->format('M Y')}}</span>
                    <span class="block text-xl">{{$date->format('j')}}</span>
                    <span class="block text-xs font-normal">{{$date->format('D')}}</span>
                </x-ui.table-th>
            @endforeach
        </tr>
        </thead>

        <x-ui.table-tbody>
            @php
                $rowIndex = 0;
                $columnIndex = 0;
            @endphp

            @foreach($tableData as $type => $accounts)
                <tr class="bg-{{strtolower($type)}} text-white uppercase">
                    <x-ui.table-td padding="py-1 pr-2 pl-4"
                                   baseClass="text-white whitespace-nowrap bg-{{strtolower($type)}} sticky left-0 z-10">
                        {{ucfirst($type)}} Accounts
                    </x-ui.table-td>
                    <x-ui.table-td attr="colspan={{$range}}">
                    </x-ui.table-td>
                </tr>
                @if($type == 'revenue')
                    @foreach($accounts as $id => $data)
                        @php
                            $rowIndex = 1;
                        @endphp
                        <tr class="bg-indigo-100 hover:bg-yellow-100 border-light_blue divide-x">
                            <x-ui.table-td padding="p-1 pl-2"
                                           baseClass="text-dark_gray sticky left-0 bg-indigo-100 z-10">
                                {{$data['name']}}
                            </x-ui.table-td>
                            {{--                        <td class="border border-gray-300 whitespace-nowrap pl-2">{{$data['name']}}</td>--}}
                            @foreach($period as $date)
                                @php
                                    $columnIndex++;
                                @endphp
                                <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
                                    <input
                                        class="px-2 py-1 text-right bg-transparent border-none w-full
                                    focus:bg-gray-100 disabled:opacity-90"
                                        id="account_{{$id}}_{{$date->format('Y-m-d')}}"
                                        type="text"
                                        value="{{number_format($data[$date->format('Y-m-d')], 0, '.', '')}}"
                                        data-row="{{$rowIndex}}"
                                        data-column="{{$columnIndex}}"
                                        disabled/>
                                </x-ui.table-td>
                            @endforeach
                        </tr>
                        @foreach($data as $key => $ext_data)
                            @if(is_array($ext_data))
                                @php
                                    $rowIndex++;
                                    $columnIndex = 0;
                                @endphp
                                <tr class="bg-white hover:bg-yellow-100 border-light_blue divide-x">
                                    <x-ui.table-td padding="p-1 pr-2 pl-6"
                                                   baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-white z-10">
                                        {{$ext_data['name']}}
                                    </x-ui.table-td>
                                    @foreach($period as $date)
                                        @php
                                            $columnIndex++;
                                        @endphp
                                        <x-ui.table-td class="text-right hover:bg-yellow-100" padding="p-0">
                                            <input
                                                class="px-2 py-1 w-full text-right bg-transparent border-0
                                            border-transparent outline-none
                                            focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                            @if(!$business->license->checkLicense)
                                                    focus:bg-gray-100
                                            @else
                                                    pfp_copy_move_element hover:bg-yellow-50 focus:bg-yellow-50
                                            @endif "
                                                @if($business->license->checkLicense) draggable="true" @endif
                                                id="flow_{{$key}}_{{$date->format('Y-m-d')}}"
                                                data-row="{{$rowIndex}}"
                                                data-column="{{$columnIndex}}"
                                                type="text"
                                                value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}"
                                                @if(!$business->license->checkLicense) disabled @endif/>
                                        </x-ui.table-td>
                                    @endforeach
                                </tr>
                                @if (isset($recurring[$key]))
                                    @foreach($recurring[$key] as $recurringTitle => $recurringData)
                                        @php
                                            $columnIndex = 0;
                                        @endphp
                                        <tr class="bg-white hover:bg-yellow-100 border-light_blue divide-x text-xs">
                                            <x-ui.table-td padding="p-1 pr-2 pl-6"
                                                           baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-white z-10">
                                                <x-icons.recurring class="w-3 h-auto inline mr-1"/>
                                                <span
                                                    title="{{$recurringData['description']}}">{{$recurringTitle}}</span>
                                            </x-ui.table-td>
                                            @foreach($period as $date)
                                                @php
                                                    $columnIndex++;
                                                    $value = $recurringData['forecast'][$date->format('Y-m-d')] ?? 0;
                                                @endphp
                                                <x-ui.table-td class="text-right hover:bg-yellow-100" padding="p-0">
                                                    @if($value)
                                                        <input
                                                            class="px-2 py-1 w-full text-right bg-transparent border-0
                                            border-transparent outline-none cursor-copy pfp_forecast_value select-none
                                            focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                            text-xs"
                                                            disabled
                                                            data-for_row="{{$rowIndex}}"
                                                            data-for_column="{{$columnIndex}}"
                                                            type="text"
                                                            value="{{number_format($value, 0, '.', '')}}"
                                                        />
                                                    @endif
                                                </x-ui.table-td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                        @endforeach
                    @endforeach
                @else
                    @foreach($accounts as $id => $data)
                        @php
                            $rowIndex++;
                            $columnIndex = 0;
                        @endphp
                        <tr class="bg-indigo-100 hover:bg-yellow-100 border-light_blue divide-x">
                            <x-ui.table-td class="text-left whitespace-nowrap bg-indigo-100 z-10 sticky left-0"
                                           padding="p-1 pr-2 pl-4">
                                {{$data['name']}}
                            </x-ui.table-td>
                            @foreach($period as $date)
                                @php
                                    $columnIndex++;
                                    $manual = $data['manual'][$date->format('Y-m-d')] ?? null;
                                @endphp
                                <x-ui.table-td class="text-right" padding="p-0">
                                    <input
                                        class="px-2 py-1 w-full text-right bg-transparent border-none disabled:opacity-90
                                        @if ($manual)
                                            bg-yellow-300 hover:bg-yellow-300 focus:bg-yellow-300
                                            @if(!$business->license->checkLicense)
                                                focus:bg-gray-100
                                            @else
                                                __pfp_copy_move_element hover:bg-yellow-50 focus:bg-yellow-50
                                            @endif
                                        @endif
                                            "
{{--                                        @if($business->license->checkLicense && !$manual)--}}
{{--                                        draggable="true"--}}
{{--                                        @endif--}}

                                        @if ($manual)
                                        title="Balance overridden"
                                        @endif

                                        type="text"
                                        id="account_{{$id}}_{{$date->format('Y-m-d')}}"
                                        value="{{is_array($data[$date->format('Y-m-d')])
                                                ? number_format($data[$date->format('Y-m-d')][0], 0, '.', '')
                                                : number_format($data[$date->format('Y-m-d')], 0, '.', '')}}"
                                        data-row="{{$rowIndex}}"
                                        data-column="{{$columnIndex}}"
{{--                                        @if($manual) disabled @endif--}}
{{--                                        @if(!$business->license->checkLicense) disabled @endif--}}
                                        disabled
                                        />
                                </x-ui.table-td>
                            @endforeach
                        </tr>
                        @foreach($data as $key => $ext_data)
                            @if(is_array($ext_data))
                                @if($key == 'transfer')
                                    @php
                                        $rowIndex++;
                                        $columnIndex = 0;
                                    @endphp
                                    <tr class="bg-white hover:bg-yellow-100 border-light_blue divide-x">
                                        <x-ui.table-td padding="p-1 pr-2 pl-6"
                                                       class="text-left whitespace-nowrap sticky left-0 bg-white z-10">
                                            {{__('Transfer In')}}
                                        </x-ui.table-td>
                                        @foreach($period as $date)
                                            @php
                                                $columnIndex++;
                                            @endphp
                                            <x-ui.table-td padding="p-0" class="text-right">
                                                <input
                                                    class="px-2 py-1 w-full text-right bg-transparent border-none
                                                    focus:bg-gray-100 disabled:opacity-90"
                                                    type="text"
                                                    value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}"
                                                    data-row="{{$rowIndex}}"
                                                    data-column="{{$columnIndex}}"
                                                    disabled/>
                                            </x-ui.table-td>
                                        @endforeach
                                    </tr>
                                @elseif($key == 'total')
                                    @php
                                        $rowIndex++;
                                        $columnIndex = 0;
                                    @endphp
                                    <tr class="bg-white hover:bg-yellow-100 border-light_blue divide-x">
                                        <x-ui.table-td padding="p-1 pr-2 pl-6"
                                                       class="text-left whitespace-nowrap sticky bg-white left-0 z-10">
                                            Flow Total
                                        </x-ui.table-td>
                                        @foreach($period as $date)
                                            @php
                                                $columnIndex++;
                                            @endphp
                                            <x-ui.table-td padding="p-0" class="text-right">
                                                <input
                                                    class="px-2 py-1 w-full text-right bg-transparent border-none
                                                    focus:bg-gray-100 disabled:opacity-90"
                                                    type="text"
                                                    value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}"
                                                    data-row="{{$rowIndex}}"
                                                    data-column="{{$columnIndex}}"
                                                    disabled/>
                                            </x-ui.table-td>
                                        @endforeach
                                    </tr>
                                @elseif(is_integer($key))
                                    @php
                                        $rowIndex++;
                                        $columnIndex = 0;
                                    @endphp
                                    <tr class="bg-indigo-100 hover:bg-yellow-100 border-light_blue divide-x">
                                        <x-ui.table-td padding="p-1 pr-2 pl-4"
                                                       class="text-left whitespace-nowrap sticky bg-indigo-100 left-0 z-10">
                                            {{$ext_data['name']}}
                                        </x-ui.table-td>
                                        @foreach($period as $date)
                                            @php
                                                $columnIndex++;
                                            @endphp
                                            <x-ui.table-td padding="p-0" class="text-right hover:bg-yellow-200">
                                                <input class="px-2 py-1 w-full text-right bg-transparent border-0
                                                border-transparent outline-none
                                                focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                                @if(!$business->license->checkLicense) focus:bg-gray-100
                                                @else pfp_copy_move_element hover:bg-yellow-50 focus:bg-yellow-50
                                                @endif "
                                                       @if($business->license->checkLicense) draggable="true" @endif

                                                       id="flow_{{$key}}_{{$date->format('Y-m-d')}}"
                                                       type="text"
                                                       data-row="{{$rowIndex}}"
                                                       data-column="{{$columnIndex}}"
                                                       value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}"
                                                       @if(!$business->license->checkLicense) disabled @endif/>
                                            </x-ui.table-td>
                                        @endforeach
                                    </tr>
                                    @if (isset($recurring[$key]))
                                        @foreach($recurring[$key] as $recurringTitle => $recurringData)
                                            @php
                                                $columnIndex = 0;
                                            @endphp
                                            <tr class="bg-white hover:bg-yellow-100 border-light_blue divide-x text-xs">
                                                <x-ui.table-td padding="p-1 pr-2 pl-6"
                                                               baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-white z-10">
                                                    <x-icons.recurring class="w-3 h-auto inline mr-1"/>
                                                    {{$recurringTitle}}
                                                </x-ui.table-td>
                                                @foreach($period as $date)
                                                    @php
                                                        $columnIndex++;
                                                        $value = $recurringData['forecast'][$date->format('Y-m-d')] ?? 0;
                                                    @endphp
                                                    <x-ui.table-td class="text-right hover:bg-yellow-100" padding="p-0">
                                                        @if($value)
                                                            <input
                                                                class="px-2 py-1 w-full text-right bg-transparent border-0
                                                                        border-transparent outline-none cursor-copy pfp_forecast_value select-none
                                                                        focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                                                        text-xs"
                                                                draggable="true"
                                                                data-for_row="{{$rowIndex}}"
                                                                data-for_column="{{$columnIndex}}"
                                                                type="text"
                                                                value="{{number_format($value, 0, '.', '')}}"
                                                            />
                                                        @endif
                                                    </x-ui.table-td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                            @endif
                        @endforeach
                        <tr class="bg-light_blue">
                            <x-ui.table-td class="text-center h-1" padding="p-0"
                                           attr="colspan={{$range+1}}"></x-ui.table-td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </x-ui.table-tbody>
    </x-ui.table-table>
    <div style="display: none;" id="php_lastData" data-last_row_index="{{$rowIndex}}"
         data-last_row_index="{{$columnIndex}}"></div>
@endif
