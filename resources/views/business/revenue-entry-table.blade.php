<x-ui.table-table class="relative mb-2 cursor-fill-data">
    <thead>
    <tr class="border-b divide-x border-light_blue">
        <x-ui.table-th class="sticky top-0 left-0 text-center"
                       baseClass="min-w-24 w-32 text-dark_gray font-normal bg-data-entry z-30">
            <span id="processCounter" class="hidden text-xs font-normal opacity-50"></span>
        </x-ui.table-th>

        @foreach($period as $dateRow)
            @php
                $date = Carbon\Carbon::parse($dateRow, 'Y-m-d');
            @endphp
            <x-ui.table-th
                class="text-center text-dark_gray sticky top-0 {{ $date == $todayShort ? 'font-bold': '' }}"
                baseClass="min-w-24 font-normal z-20 {{ $date == $todayShort ? 'bg-light_blue': 'bg-data-entry' }}">
                <span class="block text-xs font-normal">{{$date->format('M Y')}}</span>
                <span class="block text-xl">{{$date->format('j')}}</span>
                <span class="block text-xs font-normal">{{$date->format('D')}}</span>
            </x-ui.table-th>
        @endforeach
    </tr>
    </thead>

    <x-ui.table-tbody>
        <tr class="divide-x border-light_blue level_1">
            <x-ui.table-td padding="p-1 pl-4"
                           baseClass="text-dark_gray sticky left-0 z-10 bg-atlantis-200">
                {{__('Revenue')}}
            </x-ui.table-td>
            @foreach($period as $i => $date)
                <x-ui.table-td baseClass="bg-atlantis-200" padding="p-0">
                    <input
                        class="w-full px-2 py-1 text-right bg-transparent border-0 border-transparent outline-none revenue_total"
                        id="revenue_{{$date->format('Y-m-d')}}"
                        data-row="revenue"
                        data-column="{{$i+1}}"
                        type="text" pattern="[0-9]{10}" disabled
                        value="0"/>
                </x-ui.table-td>
            @endforeach
        </tr>
        <tr class="divide-x border-light_blue">
            <x-ui.table-td padding="p-1 pl-2"
                           baseClass="bg-atlantis-100 text-dark_gray sticky left-0 z-10">
                {{__('Flow total')}}
            </x-ui.table-td>
            @foreach($period as $i => $date)
                <x-ui.table-td baseClass="bg-atlantis-100" padding="p-0">
                    <input
                        class="w-full px-2 py-1 text-right bg-transparent border-0 border-transparent outline-none flow_total"
                        id="flow_total_{{$date->format('Y-m-d')}}"
                        data-row="flow_total"
                        data-column="{{$i+1}}"
                        type="text" pattern="[0-9]{10}" disabled
                        value="0"/>
                </x-ui.table-td>
            @endforeach
        </tr>

        @foreach($tableData as $accountId => $accountData)
            @if (isset($accountData['flows']) && !empty($accountData['flows']))
                <tr class="divide-x border-light_blue level_2">
                    <x-ui.table-td padding="p-1 pl-2"
                                   baseClass="text-dark_gray sticky left-0 z-10 bg-account">
                        <div class="flex mr-auto">
                            <div class="inline-flex">
                                {{$accountData['name']}}
                            </div>
                            <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId]) }})"
                               title="Create new flow for {{$accountData['name']}}" class="ml-2 cursor-pointer">
                                <x-icons.add-border class="inline-flex h-4"/>
                            </a>
                        </div>
                    </x-ui.table-td>
                    <x-ui.table-td attr="colspan={{count($period)}}" baseClass="bg-account">
                    </x-ui.table-td>
                </tr>
                @php
                    $rowIndex = 0;
                @endphp
                @foreach($accountData['flows'] as $flowId => $flowData)
                    @php
                        $rowIndex++;
                        $columnIndex = 0;
                    @endphp
                    <tr class="divide-x border-light_blue level_3">
                        <x-ui.table-td padding="p-1 pl-2 pr-2"
                                       baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-data-entry z-10 text-left">
                            <div class="flex mr-auto">
                                <div
                                    class="inline-flex text-{{$flowData['negative'] ? 'red-500' : 'green' }} text-2xl pr-2 w-5 leading-none">
                                    {!! $flowData['negative'] ? '&ndash;' : '+' !!}
                                </div>
                                <div class="inline-flex flex-grow">
                                    {{$flowData['label']}}
                                </div>
                                <div class="inline-flex px-4 text-right">
                                    ({{$flowData['certainty']}}%)
                                </div>
                                <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId, 'routeName' => 'revenue-entry.table']) }})"
                                   title="Edit {{$flowData['label']}}" class="cursor-pointer">
                                    <x-icons.edit class="inline-flex self-end h-3 ml-auto"/>
                                </a>
                                <a onclick="Livewire.emit('openModal', 'modal-quick-entry-data-for-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId]) }})"
                                   title="Quick Entry for Flow {{$flowData['label']}}" class="cursor-pointer ml-3">
                                    <x-icons.fill class="inline-flex self-end h-3 ml-auto text-green"/>
                                </a>
                            </div>
                        </x-ui.table-td>
                        @foreach($period as $date)
                            @php
                                $columnIndex++;
                            @endphp
                            <x-ui.table-td class="text-right " padding="p-0">
                                <input draggable="true" class="flow_cell px-2 py-1 w-full text-right bg-transparent border-0
                                            border-transparent outline-none pfp_copy_move_element
                                            focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                            @if(!$business->license->checkLicense)
                                    focus:bg-gray-100
@else
                                    hover:bg-yellow-50 focus:bg-yellow-50
@endif "
                                       id="flow_{{$flowId}}_{{$date->format('Y-m-d')}}"
                                       data-row="{{$rowIndex}}"
                                       data-column="{{$columnIndex}}"
                                       data-certainty="{{$flowData['certainty']}}"
                                       data-negative="{{$flowData['negative']}}"
                                       type="text" pattern="[0-9]{10}"
                                       @if(isset($flowData['allocations'][$date->format('Y-m-d')]))
                                       value="{{$flowData['allocations'][$date->format('Y-m-d')]['amount']}}"
                                       @else
                                       value="0"
                                       @endif
                                       @if(!$business->license->checkLicense) disabled @endif/>
                            </x-ui.table-td>
                        @endforeach
                    </tr>

                @endforeach
            @endif
        @endforeach

    </x-ui.table-tbody>
</x-ui.table-table>
