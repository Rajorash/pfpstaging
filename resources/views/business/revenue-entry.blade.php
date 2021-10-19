<x-app-layout>
    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        {{__('Revenue Entry')}}
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-slot name="subHeader">
        <div class="flex items-center content-between">
            <input type="hidden" id="businessId" name="businessId" value="{{$business->id}}"/>
            <div class="p-2">
                <label class="mr-2" for="startdate">{{__('Start date')}}</label>
                <input name="startdate" id="startDate"
                       min="{{$minDate}}" max="{{$maxDate}}"
                       class="py-1 my-0 rounded form-input" type="date"
                       value="{{$startDate}}">
            </div>
            <div class="p-2">
                <label class="mr-2" for="range">{{__('Range')}}</label>
                <select name="range" id="currentRangeValue" class="py-1 my-0 rounded form-select">
                    @foreach ($rangeArray as $key => $value)
                        <option value="{{$key}}" @if($key == $currentRangeValue) selected @endif>{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <x-ui.data-submit-controls class="items-center p-2" :heightController="true" :autoSubmit="false"/>

        </div>
    </x-slot>

    @if(!$business->license->checkLicense)
        <div class="font-bold text-center text-red-500">{{__('License is inactive. Edit data forbidden.')}}</div>
    @endif


    <x-ui.main width="w-full">
        {{--        <div id="revenueTablePlace"--}}
        {{--             class="relative overflow-scroll global_nice_scroll block_different_height return_coordinates_table">--}}
        {{--            <div class="p-8 text-center opacity-50">...loading</div>--}}
        {{--        </div>--}}
        <div id="--revenueTablePlace"
             class="relative overflow-scroll global_nice_scroll block_different_height return_coordinates_table">

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
                    @foreach($tableData as $accountId => $accountData)
                        @if (isset($accountData['flows']) && !empty($accountData['flows']))
                            <tr class="divide-x bg-account hover:bg-yellow-100 border-light_blue">
                                <x-ui.table-td padding="p-1 pl-2"
                                               baseClass="text-dark_gray sticky left-0 bg-account z-10">
                                    {{$accountData['name']}}
                                </x-ui.table-td>
                            </tr>

                            @foreach($accountData['flows'] as $flowId => $flowData)
                                <tr class="divide-x border-light_blue">
                                    <x-ui.table-td padding="p-1 pl-2"
                                                   baseClass="text-dark_gray sticky left-0 bg-account z-10">
                                        {{$flowData['label']}}
                                    </x-ui.table-td>
                                    @foreach($period as $date)
                                        <x-ui.table-td class="text-right " padding="p-0">
                                            @if(isset($flowData['allocations'][$date->format('Y-m-d')]))
                                                {{$flowData['allocations'][$date->format('Y-m-d')]['amount']}}
                                            @else
                                                0
                                            @endif
                                        </x-ui.table-td>
                                    @endforeach
                                </tr>

                                @if (isset($flowData['recurring']) && !empty($flowData['recurring']))

                                    @foreach ($flowData['recurring'] as $recurringData)
                                        <tr class="divide-x border-light_blue">
                                            <x-ui.table-td padding="p-1 pl-2"
                                                           baseClass="text-dark_gray sticky left-0 bg-account z-10">
                                                <x-icons.recurring class="inline w-3 h-auto mr-1"/> {{$recurringData['title']}}
                                            </x-ui.table-td>
                                            @foreach($period as $date)
                                                <x-ui.table-td class="text-right " padding="p-0">
                                                    @if(isset($recurringData['forecast'][$date->format('Y-m-d')]))
                                                        {{$recurringData['forecast'][$date->format('Y-m-d')]}}
                                                    @else
                                                        0
                                                    @endif
                                                </x-ui.table-td>
                                            @endforeach
                                        </tr>
                                    @endforeach

                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </x-ui.table-tbody>
            </x-ui.table-table>


        </div>
    </x-ui.main>

    <x-spinner-block/>

    <script type="text/javascript">
        window.revenueControllerUpdate = "{{route('revenue-entry.updateData')}}";
    </script>
</x-app-layout>
