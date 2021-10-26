@if (!$phase)
    <x-ui.error class="block p-12">{{__('Date is to fare or late. Set another date.')}}</x-ui.error>
@else
    <x-ui.table-table class="relative mb-2 cursor-fill-data">
        <thead>
        <tr class="border-b divide-x border-light_blue">
            <x-ui.table-th class="sticky top-0 left-0 text-center"
                           baseClass="min-w-24 w-32 text-dark_gray font-normal bg-data-entry z-30">
                <span id="processCounter" class="hidden text-xs font-normal opacity-50"></span>
            </x-ui.table-th>

            @foreach($period as $date)
                @php
                    $date = Carbon\Carbon::parse($date, 'Y-m-d H:i:s');
                @endphp
                <x-ui.table-th class="text-center {{ $date->isToday() ? 'text-blue': 'text-dark_gray' }} sticky top-0"
                               baseClass="min-w-24 font-normal bg-data-entry z-20">
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

            @if(isset($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE.'_total']))
                @foreach ($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE.'_total'] as $accountId => $data)
                    @php
                        $rowIndex = 1;
                    @endphp
                    <tr class="divide-x bg-atlantis-200 border-light_blue">
                        <x-ui.table-td padding="p-1 pl-4"
                                       baseClass="text-dark_gray sticky left-0 bg-atlantis-200 z-10">
                            {{$data['name']}} {{__('total')}}
                        </x-ui.table-td>
                        @foreach($period as $date)
                            @php
                                $columnIndex++;
                            @endphp
                            <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
                                <input
                                    class="w-full px-2 py-1 text-right bg-transparent border-none"
                                    id="account_{{$accountId}}_{{$date->format('Y-m-d')}}"
                                    type="text" pattern="[0-9]{10}"
                                    value="{{ (isset($data[$date->format('Y-m-d')]) ? number_format($data[$date->format('Y-m-d')], 2, '.', '') : '0') }}"
                                    data-row="{{$rowIndex}}"
                                    data-column="{{$columnIndex}}"
                                    disabled/>
                            </x-ui.table-td>
                        @endforeach
                    </tr>
                @endforeach
            @endif

            @foreach($tableData as $type => $accounts)
                @if($type == 'revenue_total')


                @elseif($type == 'revenue')
{{--                    @foreach($accounts as $id => $data)--}}
{{--                        @php--}}
{{--                            $rowIndex = 1;--}}
{{--                        @endphp--}}
{{--                        <tr class="divide-x bg-account hover:bg-yellow-100 border-light_blue">--}}
{{--                            <x-ui.table-td padding="p-1 pl-2"--}}
{{--                                           baseClass="text-dark_gray sticky left-0 bg-account z-10">--}}
{{--                                {{$data['name']}}--}}
{{--                            </x-ui.table-td>--}}
{{--                            --}}{{--                        <td class="pl-2 border border-gray-300 whitespace-nowrap">{{$data['name']}}</td>--}}
{{--                            @foreach($period as $date)--}}
{{--                                @php--}}
{{--                                    $columnIndex++;--}}
{{--                                @endphp--}}
{{--                                <x-ui.table-td class="text-right" padding="p-0" attr="disabled">--}}
{{--                                    <input--}}
{{--                                        class="w-full px-2 py-1 text-right bg-transparent border-none focus:bg-gray-100 disabled:opacity-90"--}}
{{--                                        id="account_{{$id}}_{{$date->format('Y-m-d')}}"--}}
{{--                                        type="text" pattern="[0-9]{10}"--}}
{{--                                        value="{{number_format($data[$date->format('Y-m-d')], 0, '.', '')}}"--}}
{{--                                        data-row="{{$rowIndex}}"--}}
{{--                                        data-column="{{$columnIndex}}"--}}
{{--                                        disabled/>--}}
{{--                                </x-ui.table-td>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        @foreach($data as $key => $ext_data)--}}
{{--                            @if(is_array($ext_data))--}}
{{--                                @php--}}
{{--                                    $rowIndex++;--}}
{{--                                    $columnIndex = 0;--}}
{{--                                @endphp--}}
{{--                                <tr class="divide-x bg-data-entry hover:bg-yellow-100 border-light_blue">--}}
{{--                                    <x-ui.table-td padding="p-1 pr-2 pl-6"--}}
{{--                                                   baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-data-entry z-10">--}}
{{--                                        {{$ext_data['name']}}--}}
{{--                                    </x-ui.table-td>--}}
{{--                                    @foreach($period as $date)--}}
{{--                                        @php--}}
{{--                                            $columnIndex++;--}}
{{--                                        @endphp--}}
{{--                                        <x-ui.table-td class="text-right hover:bg-yellow-100" padding="p-0">--}}
{{--                                            <input--}}
{{--                                                class="px-2 py-1 w-full text-right bg-transparent border-0--}}
{{--                                            border-transparent outline-none--}}
{{--                                            focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90--}}
{{--                                            @if(!$business->license->checkLicense)--}}
{{--                                                    focus:bg-gray-100--}}
{{--                                            @else--}}
{{--                                                    pfp_copy_move_element hover:bg-yellow-50 focus:bg-yellow-50--}}
{{--                                            @endif "--}}
{{--                                                @if($business->license->checkLicense) draggable="true" @endif--}}
{{--                                                id="flow_{{$key}}_{{$date->format('Y-m-d')}}"--}}
{{--                                                data-row="{{$rowIndex}}"--}}
{{--                                                data-column="{{$columnIndex}}"--}}
{{--                                                type="text" pattern="[0-9]{10}"--}}
{{--                                                value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}"--}}
{{--                                                @if(!$business->license->checkLicense) disabled @endif/>--}}
{{--                                        </x-ui.table-td>--}}
{{--                                    @endforeach--}}
{{--                                </tr>--}}
{{--                                --}}{{--                                @if (isset($recurring[$key]))--}}
{{--                                --}}{{--                                    @foreach($recurring[$key] as $recurringTitle => $recurringData)--}}
{{--                                --}}{{--                                        @php--}}
{{--                                --}}{{--                                            $columnIndex = 0;--}}
{{--                                --}}{{--                                        @endphp--}}
{{--                                --}}{{--                                        <tr class="text-xs divide-x bg-recurring hover:bg-yellow-100 border-light_blue">--}}
{{--                                --}}{{--                                            <x-ui.table-td padding="p-1 pr-2 pl-6"--}}
{{--                                --}}{{--                                                           baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-recurring z-10">--}}
{{--                                --}}{{--                                                <x-icons.recurring class="inline w-3 h-auto mr-1"/>--}}
{{--                                --}}{{--                                                <span--}}
{{--                                --}}{{--                                                    title="{{$recurringData['description']}}">{{$recurringTitle}}</span>--}}
{{--                                --}}{{--                                            </x-ui.table-td>--}}
{{--                                --}}{{--                                            @foreach($period as $date)--}}
{{--                                --}}{{--                                                @php--}}
{{--                                --}}{{--                                                    $columnIndex++;--}}
{{--                                --}}{{--                                                    $value = $recurringData['forecast'][$date->format('Y-m-d')] ?? 0;--}}
{{--                                --}}{{--                                                @endphp--}}
{{--                                --}}{{--                                                <x-ui.table-td class="text-right bg-recurring hover:bg-yellow-100" padding="p-0">--}}
{{--                                --}}{{--                                                    @if($value)--}}
{{--                                --}}{{--                                                        <input--}}
{{--                                --}}{{--                                                            class="w-full px-2 py-1 text-xs text-right bg-transparent border-0 border-transparent outline-none select-none cursor-copy pfp_forecast_value focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90"--}}
{{--                                --}}{{--                                                            disabled--}}
{{--                                --}}{{--                                                            data-for_row="{{$rowIndex}}"--}}
{{--                                --}}{{--                                                            data-for_column="{{$columnIndex}}"--}}
{{--                                --}}{{--                                                            type="text" pattern="[0-9]{10}"--}}
{{--                                --}}{{--                                                            value="{{number_format($value, 0, '.', '')}}"--}}
{{--                                --}}{{--                                                        />--}}
{{--                                --}}{{--                                                    @endif--}}
{{--                                --}}{{--                                                </x-ui.table-td>--}}
{{--                                --}}{{--                                            @endforeach--}}
{{--                                --}}{{--                                        </tr>--}}
{{--                                --}}{{--                                    @endforeach--}}
{{--                                --}}{{--                                @endif--}}
{{--                            @endif--}}
{{--                        @endforeach--}}
{{--                    @endforeach--}}
                @else
                    @foreach($accounts as $id => $data)
                        @php
                            $rowIndex++;
                            $columnIndex = 0;
                        @endphp
                        <tr class="divide-x bg-account hover:bg-yellow-100 border-light_blue">
                            <x-ui.table-td class="sticky left-0 z-10 text-left bg-account whitespace-nowrap"
                                           padding="p-1 pr-2 pl-4">

                                <div class="flex mr-auto">
                                    <div class="inline-flex">
                                        {{$data['name']}}
                                    </div>
                                    <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $id]) }})"
                                       title="Create new flow for {{$data['name']}}" class="ml-2 cursor-pointer">
                                        <x-icons.add-border class="inline-flex h-4"/>
                                    </a>
                                </div>
                            </x-ui.table-td>
                            @foreach($period as $date)
                                @php
                                    $columnIndex++;
                                    $manual = $data['manual'][$date->format('Y-m-d')] ?? null;
                                @endphp
                                <x-ui.table-td class="text-right bg-account" padding="p-0">
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

                                        type="text" pattern="[0-9]{10}"
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
                                    <tr class="divide-x bg-readonly hover:bg-yellow-100 border-light_blue">
                                        <x-ui.table-td padding="p-1 pr-2 pl-6"
                                                       class="sticky left-0 z-10 text-left bg-readonly whitespace-nowrap">
                                            {{__('Transfer In')}}
                                        </x-ui.table-td>
                                        @foreach($period as $date)
                                            @php
                                                $columnIndex++;
                                            @endphp
                                            <x-ui.table-td padding="p-0" class="text-right">
                                                <input
                                                    class="w-full px-2 py-1 text-right bg-transparent border-none focus:bg-gray-100 disabled:opacity-90"
                                                    type="text" pattern="[0-9]{10}"
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
                                    <tr class="divide-x bg-readonly hover:bg-yellow-100 border-light_blue">
                                        <x-ui.table-td padding="p-1 pr-2 pl-6"
                                                       class="sticky left-0 z-10 text-left bg-readonly whitespace-nowrap">
                                            {{__('Flow Total')}}
                                        </x-ui.table-td>
                                        @foreach($period as $date)
                                            @php
                                                $columnIndex++;
                                            @endphp
                                            <x-ui.table-td padding="p-0" class="text-right">
                                                <input
                                                    class="w-full px-2 py-1 text-right bg-transparent border-none focus:bg-gray-100 disabled:opacity-90"
                                                    type="text" pattern="[0-9]{10}"
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
                                    <tr class="divide-x bg-data-entry hover:bg-yellow-100 border-light_blue">
                                        <x-ui.table-td padding="p-1 pr-2 pl-4"
                                                       class="sticky left-0 z-10 text-left bg-data-entry whitespace-nowrap">
                                            <div class="flex mr-auto">
                                                <div
                                                    class="inline-flex
                                                    text-{{$ext_data['negative'] ? 'red-500' : 'green' }}
                                                        text-2xl pr-2 w-5 leading-none">
                                                    {!! $ext_data['negative'] ? '&ndash;' : '+' !!}
                                                </div>
                                                <div class="inline-flex flex-grow">
                                                    {{$ext_data['name']}}
                                                </div>
                                                <div class="inline-flex px-4 text-right">
                                                    ({{$ext_data['certainty']}}%)
                                                </div>
                                                <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $id, 'flowId' => $key]) }})"
                                                   title="Edit {{$ext_data['name']}}" class="cursor-pointer">
                                                    <x-icons.edit class="inline-flex self-end h-3 ml-auto"/>
                                                </a>
                                                <a onclick="Livewire.emit('openModal', 'modal-quick-entry-data-for-flow',  {{ json_encode(['accountId' => $id, 'flowId' => $key]) }})"
                                                   title="Quick Entry for Flow {{$ext_data['name']}}" class="cursor-pointer ml-3">
                                                    <x-icons.fill class="inline-flex self-end h-3 ml-auto text-green"/>
                                                </a>
                                            </div>
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
                                                       type="text" pattern="[0-9]{10}"
                                                       data-row="{{$rowIndex}}"
                                                       data-column="{{$columnIndex}}"
                                                       data-certainty="{{$ext_data['certainty']}}"
                                                       data-negative="{{$ext_data['negative']}}"
                                                       value="{{isset($ext_data[$date->format('Y-m-d')]) ? number_format($ext_data[$date->format('Y-m-d')], 0, '.', '') : '0'}}"
                                                       @if(!$business->license->checkLicense) disabled @endif/>
                                            </x-ui.table-td>
                                        @endforeach
                                    </tr>
{{--                                    @if (isset($recurring[$key]))--}}
{{--                                        @foreach($recurring[$key] as $recurringTitle => $recurringData)--}}
{{--                                            @php--}}
{{--                                                $columnIndex = 0;--}}
{{--                                            @endphp--}}
{{--                                            <tr class="text-xs divide-x bg-recurring hover:bg-yellow-100 border-light_blue">--}}
{{--                                                <x-ui.table-td padding="p-1 pr-2 pl-6"--}}
{{--                                                               baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-recurring z-10">--}}
{{--                                                    <x-icons.recurring class="inline w-3 h-auto mr-1"/>--}}
{{--                                                    {{$recurringTitle}}--}}
{{--                                                </x-ui.table-td>--}}
{{--                                                @foreach($period as $date)--}}
{{--                                                    @php--}}
{{--                                                        $columnIndex++;--}}
{{--                                                        $value = $recurringData['forecast'][$date->format('Y-m-d')] ?? 0;--}}
{{--                                                    @endphp--}}
{{--                                                    <x-ui.table-td class="text-right bg-recurring hover:bg-yellow-100"--}}
{{--                                                                   padding="p-0">--}}
{{--                                                        @if($value)--}}
{{--                                                            <input--}}
{{--                                                                class="w-full px-2 py-1 text-xs text-right bg-transparent border-0 border-transparent outline-none select-none cursor-copy pfp_forecast_value focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90"--}}
{{--                                                                draggable="true"--}}
{{--                                                                data-for_row="{{$rowIndex}}"--}}
{{--                                                                data-for_column="{{$columnIndex}}"--}}
{{--                                                                type="text" pattern="[0-9]{10}"--}}
{{--                                                                value="{{number_format($value, 0, '.', '')}}"--}}
{{--                                                            />--}}
{{--                                                        @endif--}}
{{--                                                    </x-ui.table-td>--}}
{{--                                                @endforeach--}}
{{--                                            </tr>--}}
{{--                                        @endforeach--}}
{{--                                    @endif--}}
                                @endif
                            @endif
                        @endforeach
                        <tr class="bg-light_blue">
                            <x-ui.table-td class="h-1 text-center" padding="p-0"
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
