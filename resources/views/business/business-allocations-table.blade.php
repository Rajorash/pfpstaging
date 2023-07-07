@php
    $checkLicense = $business->license->checkLicense;
    $rowIndex = 0;
    $columnIndex = 0;

    $seats_count = 0;
    
    $conditions = checkNegativeLicense($seatsCount, $business->license->id);
  
    $checkSeatNegPos = $conditions['seats_count'];
    $licenseActiveInactive = $conditions['licenseActiveInactive'];
@endphp

<x-ui.table-table class="relative mb-2 cursor-fill-data">

    <thead {!! $tableAttributes !!}>
    <tr class="border-b divide-x border-light_blue">
        <x-ui.table-th class="sticky top-0 left-0 text-center"
                       baseClass="min-w-24 w-32 text-dark_gray font-normal bg-data-entry z-30">
            <span id="processCounter" class="hidden text-xs font-normal opacity-50"></span>
        </x-ui.table-th>

        @foreach($period as $date)
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

        @if ($projectionMode == 'expense')

            @if(isset($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE]))
                @foreach ($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE] as $accountId => $accountData)
                    <tr class="divide-x border-light_blue bg-atlantis-200 level_1 revenue-row">
                        <x-ui.table-td padding="p-1 pl-4"
                                       baseClass="text-dark_gray sticky left-0 z-10 bg-atlantis-200 whitespace-nowrap">
                            {{$accountData['name']}}
                        </x-ui.table-td>

                        @foreach($periodDates as $currentDate)
                            @php
                                $value = $accountData['total_db'][$currentDate] ?? 0;
                            @endphp
                            <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
                                <input
                                    class="w-full px-2 py-1 text-right border-none bg-atlantis-200
                                can_apply_negative_class
                                @if ($value < 0) allocation-negative-value @endif "
                                    type="text" pattern="[0-9]{10}"
                                    value="{{ number_format($value, 0, '.', '')}}"
                                    disabled/>
                            </x-ui.table-td>
                        @endforeach
                    </tr>
                @endforeach
            @endif

        @endif

        @foreach ($tableData as $accountType => $accountsArray)
            @if($accountType != \App\Models\BankAccount::ACCOUNT_TYPE_REVENUE)
                @foreach ($accountsArray as $accountId => $accountData)
                    @foreach ($accountsSubTypes as $subType => $subTypeArray)
                        <tr data-account_id="{{$accountId}}"
                            class="divide-x border-light_blue {{$subTypeArray['class_tr']}}
                            @if($subType == '_dates') level_1 @elseif( $subType  == 'sub_total') sub_level @else level_2 @endif">
                            <x-ui.table-td padding="p-1 {{$subTypeArray['class_tr']}} {{$subTypeArray['class_th']}}"
                                           baseClass="text-dark_gray sticky left-0 z-10">
                                <div class="flex mr-auto">
                                    @if (($subType == 'total' || $subType == '_dates' || $subType  == 'sub_total') && $projectionMode == 'expense')
                                        <div data-account_id="{{$accountId}}"
                                             class="inline-flex show_hide_sub-elements mr-2 opacity-50 hover:opacity-100 transition-all text-gray-700 opened">
                                            <span class="show_sub-elements @if( $subType  == 'sub_total') ml-4 @endif">
                                                <x-icons.chevron-circle-down
                                                    class="w-3 h-auto inline-block align-middle"/>
                                            </span>
                                            <span class="hide_sub-elements hidden @if( $subType  == 'sub_total') ml-4 @endif">
                                                <x-icons.chevron-circle-up
                                                    class="w-3 h-auto inline-block align-middle"/>
                                            </span>
                                        </div>
                                    @else
                                        @if ($projectionMode == 'expense')
                                            <div class="w-3 mr-2"></div>
                                        @else
                                            <div class="w-1 mr-0"></div>
                                        @endif
                                    @endif

                                    @if($subType == '_dates')
                                        <div class="inline-flex pr-4 whitespace-nowrap">
                                            {{$accountData['name']}}
                                        </div>
                                        @if ($projectionMode == 'expense')
                                            <a onclick="Livewire.emit('openModal', 'modal-flow', {{ json_encode(['accountId' => $accountId, 'flowId' => 0, 'defaultNegative' => true]) }})"
                                               title="Create new flow for {{$accountData['name']}}"
                                               class="ml-auto cursor-pointer add-flow-btn">
                                                <x-icons.add-border class="inline-flex h-4"/>
                                            </a>
                                        @endif
                                    @else
                                        <div class="inline-flex">
                                            {{$subTypeArray['title']}}
                                        </div>
                                    @endif
                                </div>
                            </x-ui.table-td>
                            @if (array_key_exists($subType, $accountData))
                                @foreach($periodDates as $currentDate)
                                    @php
                                        //$currentDate = $date->format('Y-m-d');
                                        $value = $accountData[$subType][$currentDate] ?? 0;
                                        //$columnIndex++;
                                        $manual = $subType == '_dates' ? ($accountData['manual'][$currentDate] ?? null) : null;
                                    @endphp
                                    <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
                                        <input
                                            class="w-full px-2 py-1 text-right bg-transparent border-none disabled:opacity-90
                                                @if ($manual)
                                                bg-yellow-300 hover:bg-yellow-300 focus:bg-yellow-300
@endif

                                            @if(!$checkLicense)
                                                focus:bg-gray-100
                                            @else
                                                hover:bg-yellow-50 focus:bg-yellow-50
                                            @endif

                                            @if($subType == '_dates')
                                                can_apply_negative_class
                                                @if ($value < 0)
                                                allocation-negative-value
@endif
                                            @endif
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


                    @if ($projectionMode == 'expense')

                        @if (array_key_exists('flows',$accountData))
                            @foreach ($accountData['flows'] as $flowId => $flowData)
                                @php
                                    $rowIndex++;
                                    $columnIndex = 0;
                                @endphp
                                <tr  @if($checkLicense) draggable="true" drag-root @endif  data-account_id="{{$accountId}}" 
                                flowId="{{$flowId}}"  class="divide-x bg-data-entry hover:bg-yellow-100 border-light_blue level_3">
                                    <x-ui.table-td padding="p-1 pr-2 pl-4"
                                                   class="sticky left-0 z-10 text-left bg-data-entry whitespace-nowrap">
                                        <div class="flex mr-auto" onmousedown="mouseDown()">
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
                                            <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId, 'routeName' => 'allocations-new']) }})"
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
                                    @foreach($periodDates as $currentDate)
                                        @php
                                            $columnIndex++;
                                            //$currentDate = $date->format('Y-m-d');
                                            $value = $flowData['_dates'][$currentDate] ?? 0;
                                            $conditions = checkNegativeLicense($seatsCount, $business->license->id);
                                            
                                            $checkSeatNegPos = $conditions['seats_count'];
                                            $licenseActiveInactive = $conditions['licenseActiveInactive'];
                                        @endphp
                                        <x-ui.table-td padding="p-0" class="text-right hover:bg-yellow-200">
                                            <input class="validseatcount px-2 py-1 w-full text-right bg-transparent border-0
                                                border-transparent outline-none
                                                focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
                                                @if($checkSeatNegPos && $licenseActiveInactive) focus:bg-gray-100
                                                @else pfp_copy_move_element hover:bg-yellow-50 focus:bg-yellow-50
                                                @endif
                                                "
                                                   @if($checkSeatNegPos && $licenseActiveInactive) draggable="true" drag-input @endif

                                                   id="flow_{{$flowId}}_{{$currentDate}}"
                                                   type="text" pattern="[0-9]{10}"
                                                   data-row="{{$rowIndex}}"
                                                   data-column="{{$columnIndex}}"
                                                   data-certainty="{{$flowData['certainty']}}"
                                                   data-negative="{{$flowData['negative_flow']}}"
                                                   value="{{$value}}"
                                                   @if($checkSeatNegPos && $licenseActiveInactive) @else disabled @endif/>
                                        </x-ui.table-td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif

                    @endif

                @endforeach
            @endif

            @if ($projectionMode == 'expense')
                <tr class="bg-light_blue level_2">
                    <x-ui.table-td class="h-1 text-center" padding="p-0"
                                   attr="colspan={{count($period)+1}}"></x-ui.table-td>
                </tr>
            @endif
        @endforeach

        @if ($projectionMode == 'forecast' && $business->flag == 1)
        <tr class="total_row">
                        <x-ui.table-td class="pl-5" padding="pl-0" attr="disabled"> 
                                        <?php  echo "Total"; ?>
                                    </x-ui.table-td>
                        
                       
         </tr>
         @endif

    </x-ui.table-tbody>

</x-ui.table-table>

<script>

    $(document).ready(function(){
        var licenceStatus = '{{$licenseActiveInactive}}';
        if(window.seatCount>=0 && licenceStatus==1){
            $('.validseatcount').removeAttr('disabled');
        }
      
        var checktrid = [];
        var checktdid = [];
        var total = 0;
        var periodDates = '<?php echo json_encode($periodDates); ?>';
        periodDates = JSON.parse(periodDates);

        var i = 0;

        $('tbody tr').each(function() {
            if($(this).attr('data-account_id') !== undefined){
                checktrid.push($(this).attr('data-account_id'));
            }
         })


              for(var k = 0; k < periodDates.length; k++){
                for(var l = 0; l < checktrid.length; l++){
                    if($('#account_'+checktrid[l]+'_'+periodDates[k]).val() !== undefined){
                        total = total + parseInt($('#account_'+checktrid[l]+'_'+periodDates[k]).val());
                        i++;
                    }
                }

               var div_html =  "<td class='pr-2 text-right text-dark_gray' padding='p-0' attr='disabled'>"+ total +
                 "</td>";

                 if(i == checktrid.length){
                    total = 0;
                    i = 0;
                    }
                
                $(".total_row").append(div_html);
                   
             }
            
    })   
</script>

