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
        <tr class="divide-x border-light_blue">
            <x-ui.table-td padding="p-1 pl-2"
                           baseClass="text-dark_gray sticky left-0 z-10 bg-atlantis-200">
                {{__('Revenue')}}
            </x-ui.table-td>
            @foreach($period as $i => $date)
                <x-ui.table-td baseClass="bg-atlantis-200" padding="p-0">
                    <input class="w-full px-2 py-1 text-right bg-transparent border-0 border-transparent outline-none revenue_total"
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
                    <input class="w-full px-2 py-1 text-right bg-transparent border-0 border-transparent outline-none flow_total"
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
                <tr class="divide-x border-light_blue">
                    <x-ui.table-td padding="p-1 pl-2"
                                   baseClass="text-dark_gray sticky left-0 z-10 bg-account">
                        {{$accountData['name']}}
                        <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId]) }})"
                           title="Create new flow for {{$accountData['name']}}" class="ml-2 text-xs cursor-pointer">[add flow]</a>
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
                    <tr class="divide-x border-light_blue">
                        <x-ui.table-td padding="p-1 pl-6 pr-2"
                                       baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-data-entry z-10 text-left">
                            <div class="flex mr-auto">
                                <div class="inline-flex flex-grow">
                                    {{$flowData['label']}}
                                </div>
                                <div class="inline-flex px-4 text-right">
                                    ({{$flowData['certainty']}}%)
                                </div>
                                <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId]) }})"
                                   title="Edit {{$flowData['label']}}" class="cursor-pointer">
                                    <x-icons.edit class="inline-flex self-end h-3 ml-auto" />
                                </a>
                            </div>
                        </x-ui.table-td>
                        @foreach($period as $date)
                            @php
                                $columnIndex++;
                            @endphp
                            <x-ui.table-td class="text-right " padding="p-0">
                                <input class="flow_cell px-2 py-1 w-full text-right bg-transparent border-0
                                            border-transparent outline-none
                                            focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                            @if(!$business->license->checkLicense)
                                    focus:bg-gray-100
@else
                                    hover:bg-yellow-50 focus:bg-yellow-50
@endif "
                                       id="flow_{{$flowId}}_{{$date->format('Y-m-d')}}"
                                       data-row="{{$rowIndex}}"
                                       data-column="{{$columnIndex}}"
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

                    @if (isset($flowData['recurring']) && !empty($flowData['recurring']))

                        @foreach ($flowData['recurring'] as $recurringData)
                            @php
                                $rowIndex++;
                                $columnIndex = 0;
                            @endphp
                            <tr class="divide-x border-light_blue">
                                <x-ui.table-td padding="p-1 pr-2 pl-6 "
                                               baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-recurring z-10 text-left">
                                    <x-icons.recurring
                                        class="inline w-3 h-auto mr-1"/> {{$recurringData['title']}}
                                </x-ui.table-td>
                                @foreach($period as $date)
                                    @php
                                        $columnIndex++;
                                    @endphp
                                    <x-ui.table-td class="text-right bg-recurring" padding="p-0">
                                        <input class="px-2 py-1 w-full text-right bg-transparent border-0
                                                    border-transparent outline-none
                                                    focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                                    @if(!$business->license->checkLicense)
                                            focus:bg-gray-100
@else
                                            hover:bg-yellow-50 focus:bg-yellow-50
@endif "
                                               id="flow_{{$flowId}}_{{$date->format('Y-m-d')}}"
                                               data-row="{{$rowIndex}}"
                                               data-column="{{$columnIndex}}"
                                               type="text" pattern="[0-9]{10}"
                                               @if(isset($recurringData['forecast'][$date->format('Y-m-d')]))
                                               value="{{$recurringData['forecast'][$date->format('Y-m-d')]}}"
                                               @else
                                               value="0"
                                               @endif
                                               disabled/>
                                    </x-ui.table-td>
                                @endforeach
                            </tr>
                        @endforeach

                    @endif
                @endforeach
            @endif
        @endforeach

        @if (isset($tableData['pipelines']) && !empty($tableData['pipelines']))
            <tr class="divide-x border-light_blue">
                <x-ui.table-td padding="p-1 pl-2"
                               baseClass="bg-atlantis-100 text-dark_gray sticky left-0 z-10">
                    {{__('Pipeline total')}}
                </x-ui.table-td>
                @foreach($period as $i => $date)
                    <x-ui.table-td baseClass="bg-atlantis-100" padding="p-0">
                        <input class="w-full px-2 py-1 text-right bg-transparent border-0 border-transparent outline-none pipeline_total"
                               id="pipeline_total_{{$date->format('Y-m-d')}}"
                               data-row="pipeline_total"
                               data-column="{{$i + 1}}"
                               type="text" pattern="[0-9]{10}" disabled
                               value="0"/>
                    </x-ui.table-td>
                @endforeach
            </tr>
            @foreach ($tableData['pipelines'] as $pipelineId => $pipelineData)
                @php
                    $rowIndex++;
                    $columnIndex = 0;
                @endphp
                <tr class="divide-x border-light_blue">
                    <x-ui.table-td padding="p-1 pr-2 pl-6 "
                                   baseClass="text-dark_gray whitespace-nowrap sticky left-0 bg-recurring z-10 text-left">
                        <x-icons.chart
                            class="inline w-3 h-auto mr-1"/> {{$pipelineData['title']}}
                        ({{$pipelineData['certainty']}}%)
                    </x-ui.table-td>
                    @foreach($period as $date)
                        @php
                            $columnIndex++;
                        @endphp
                        <x-ui.table-td class="text-right bg-recurring" padding="p-0">
                            <input class="pipeline_cell px-2 py-1 w-full text-right bg-transparent border-0
                                        border-transparent outline-none
                                        focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                @if(!$business->license->checkLicense)
                                focus:bg-gray-100
                                @else
                                hover:bg-yellow-50 focus:bg-yellow-50
@endif "
                                   id="pipeline_{{$pipelineId}}_{{$date->format('Y-m-d')}}"
                                   data-row="{{$rowIndex}}"
                                   data-column="{{$columnIndex}}"
                                   data-certainty="{{$pipelineData['certainty']}}"
                                   type="text" pattern="[0-9]{10}"
                                   @if(isset($pipelineData['forecast'][$date->format('Y-m-d')]))
                                   value="{{$pipelineData['forecast'][$date->format('Y-m-d')]}}"
                                   @else
                                   value="0"
                                   @endif
                                   disabled/>
                        </x-ui.table-td>
                    @endforeach
                </tr>
            @endforeach
        @endif

    </x-ui.table-tbody>
</x-ui.table-table>
