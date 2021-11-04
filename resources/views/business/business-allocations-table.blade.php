@php
    $checkLicense = $business->license->checkLicense;
    $rowIndex = 0;
    $columnIndex = 0;
@endphp
<x-ui.table-table class="relative mb-2 cursor-fill-data">
    <thead>
    <tr class="border-b divide-x border-light_blue">
        <x-ui.table-th class="sticky top-0 left-0 text-center"
                       baseClass="min-w-24 w-32 text-dark_gray font-normal bg-data-entry z-30">
            <span id="processCounter" class="hidden text-xs font-normal opacity-50"></span>
        </x-ui.table-th>

        @foreach($period as $date)
            <x-ui.table-th
                class="text-center {{ $date->isToday() ? 'text-blue': 'text-dark_gray' }} sticky top-0"
                baseClass="min-w-24 font-normal bg-data-entry z-20">
                <span class="block text-xs font-normal">{{$date->format('M Y')}}</span>
                <span class="block text-xl">{{$date->format('j')}}</span>
                <span class="block text-xs font-normal">{{$date->format('D')}}</span>
            </x-ui.table-th>
        @endforeach
    </tr>
    </thead>

    <x-ui.table-tbody>

        @if(isset($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE]))
            @foreach ($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE] as $accountId => $accountData)
                <tr class="divide-x border-light_blue bg-atlantis-200 level_1 revenue-row">
                    <x-ui.table-td padding="p-1 pl-4"
                                   baseClass="text-dark_gray sticky left-0 z-10 bg-atlantis-200">
                        {{$accountData['name']}}
                    </x-ui.table-td>

                    @foreach($period as $date)
                        @php
                            $currentDate = $date->format('Y-m-d');
                            $value = $accountData['total_db'][$currentDate] ?? 0;
                        @endphp
                        <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
                            <input
                                class="w-full px-2 py-1 text-right border-none bg-atlantis-200
                                @if ($value < 0) allocation-negative-value @endif "
                                type="text" pattern="[0-9]{10}"
                                value="{{ number_format($value, 0, '.', '')}}"
                                disabled/>
                        </x-ui.table-td>
                    @endforeach
                </tr>
            @endforeach
        @endif

        @php
            //$rowIndex++;
        @endphp

        @foreach ($tableData as $accountType => $accountsArray)
            @if($accountType != \App\Models\BankAccount::ACCOUNT_TYPE_REVENUE)
                @foreach ($accountsArray as $accountId => $accountData)
                    @foreach ($accountsSubTypes as $subType => $subTypeArray)
                        <tr class="divide-x border-light_blue {{$subTypeArray['class_tr']}}
                        @if($subType == '_dates') level_1 @else level_2 @endif">
                            <x-ui.table-td padding="p-1 {{$subTypeArray['class_tr']}} {{$subTypeArray['class_th']}}"
                                           baseClass="text-dark_gray sticky left-0 z-10">
                                <div class="flex mr-auto">
                                    @if($subType == '_dates')
                                        <div class="inline-flex">
                                            {{$accountData['name']}}
                                        </div>
                                        <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => 0, 'defaultNegative' => true]) }})"
                                            title="Create new flow for {{$accountData['name']}}"
                                            class="ml-auto cursor-pointer add-flow-btn">
                                            <x-icons.add-border class="inline-flex h-4"/>
                                        </a>
                                    @else
                                        <div class="inline-flex">
                                            {{$subTypeArray['title']}}
                                        </div>
                                    @endif
                                </div>
                            </x-ui.table-td>
                            @if (array_key_exists($subType, $accountData))
                                @foreach($period as $date)
                                    @php
                                        $currentDate = $date->format('Y-m-d');
                                        $value = $accountData[$subType][$currentDate] ?? 0;
                                        //$columnIndex++;
                                        $manual = $subType == '_dates' ? ($accountData['manual'][$currentDate] ?? null) : null;
                                    @endphp
                                    <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
                                        <input
                                            class="w-full px-2 py-1 text-right bg-transparent border-none disabled:opacity-90
                                                     @if ($manual)
                                                bg-yellow-300 hover:bg-yellow-300 focus:bg-yellow-300
@if(!$checkLicense) focus:bg-gray-100 @else hover:bg-yellow-50 focus:bg-yellow-50 @endif
                                            @endif
                                            @if ($value < 0) allocation-negative-value @endif
                                                "
                                            @if ($manual)
                                            title="Balance overridden"
                                            @endif
                                            @if($subType == '_dates')
                                            id="account_{{$accountId}}_{{$currentDate}}"
                                            @else
                                            id="{{$subType}}_{{$accountId}}_{{$currentDate}}"
                                            @endif
                                            type="text" pattern="[0-9]{10}"
                                            value="{{ number_format($value, 0, '.', '')}}"
                                            disabled/>
                                    </x-ui.table-td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach

                    @if (array_key_exists('flows',$accountData))
                        @foreach ($accountData['flows'] as $flowId => $flowData)
                            @php
                                $rowIndex++;
                                $columnIndex = 0;
                            @endphp
                            <tr class="divide-x bg-data-entry hover:bg-yellow-100 border-light_blue level_3">
                                <x-ui.table-td padding="p-1 pr-2 pl-4"
                                               class="sticky left-0 z-10 text-left bg-data-entry whitespace-nowrap ">
                                    <div class="flex mr-auto">
                                        <div
                                            class="inline-flex
                                                    text-{{$flowData['negative_flow'] ? 'red-500' : 'green' }}
                                                text-2xl pr-2 w-5 leading-none">
                                            {!! $flowData['negative_flow'] ? '&ndash;' : '+' !!}
                                        </div>
                                        <div class="inline-flex flex-grow">
                                            {{$flowData['label']}}
                                        </div>
                                        <div class="inline-flex px-4 text-right">
                                            ({{$flowData['certainty']}}%)
                                        </div>
                                        <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId]) }})"
                                           title="Edit {{$flowData['label']}}"
                                           class="opacity-50 cursor-pointer hover:opacity-100">
                                            <x-icons.edit class="inline-flex self-end h-3 ml-auto"/>
                                        </a>
                                        <a onclick="Livewire.emit('openModal', 'modal-quick-entry-data-for-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId]) }})"
                                           title="Quick Entry for Flow {{$flowData['label']}}"
                                           class="ml-3 opacity-50 cursor-pointer hover:opacity-100">
                                            <x-icons.fill class="inline-flex self-end h-3 ml-auto text-green"/>
                                        </a>
                                    </div>
                                </x-ui.table-td>
                                @foreach($period as $date)
                                    @php
                                        $columnIndex++;
                                        $currentDate = $date->format('Y-m-d');
                                        $value = $flowData['_dates'][$currentDate] ?? 0;
                                    @endphp
                                    <x-ui.table-td padding="p-0" class="text-right hover:bg-yellow-200">
                                        <input class="px-2 py-1 w-full text-right bg-transparent border-0
                                                border-transparent outline-none
                                                focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                                @if(!$checkLicense) focus:bg-gray-100
                                                @else pfp_copy_move_element hover:bg-yellow-50 focus:bg-yellow-50
                                                @endif
                                        @if ($value < 0) allocation-negative-value @endif
                                            "
                                               @if($checkLicense) draggable="true" @endif

                                               id="flow_{{$flowId}}_{{$currentDate}}"
                                               type="text" pattern="[0-9]{10}"
                                               data-row="{{$rowIndex}}"
                                               data-column="{{$columnIndex}}"
                                               data-certainty="{{$flowData['certainty']}}"
                                               data-negative="{{$flowData['negative_flow']}}"
                                               value="{{$value}}"
                                               @if(!$checkLicense) disabled @endif/>
                                    </x-ui.table-td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif

                @endforeach
            @endif
            <tr class="bg-light_blue level_2">
                <x-ui.table-td class="h-1 text-center" padding="p-0"
                               attr="colspan={{$rangeValue+1}}"></x-ui.table-td>
            </tr>
        @endforeach

    </x-ui.table-tbody>

</x-ui.table-table>
