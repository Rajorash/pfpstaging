<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\BankAccount;

use App\Models\User;

class ExpenseExport implements FromCollection , WithHeadings
{
    protected $exportCSV;
    public array $period = [];
    public array $accountDataName = [];
    public array $valueinput = [];
    public array $columns = [];
    protected int $rowIndex = 0;

    public function __construct($exportCSV)
    {
        $this->exportCSV = $exportCSV;
        // dd($this->exportCSV);
    }

    public function headings(): array
    {

        foreach($this->exportCSV['period'] as $key => $date){
            if($key == 0){
                $this->period[$key] = '';
            }
           $this->period[$key] = $date->format('M Y')."\r\n".$date->format('j')."\r\n".$date->format('D');
        }
        return $this->period ;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function Collection()
    {   
        

    if ($this->exportCSV['projectionMode'] == 'expense'){
        if(isset($this->exportCSV['tableData'][BankAccount::ACCOUNT_TYPE_REVENUE])){
            foreach ($this->exportCSV['tableData'][BankAccount::ACCOUNT_TYPE_REVENUE] as $accountId => $accountData){
              $this->accountDataName[]  = $accountData['name'];
                foreach($this->exportCSV['periodDates'] as $cdateKey => $currentDate){
                    $value = $accountData['total_db'][$currentDate] ?? 0;
                    $value= number_format($value, 0, '.', '');
                    $this->valueinput[$cdateKey] = $value;
                }
            }   
        }
    }

    [$this->accountDataName,$this->valueinput];
    // ,$this->valueinput];


    //     @if(isset($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE]))
    //         @foreach ($tableData[\App\Models\BankAccount::ACCOUNT_TYPE_REVENUE] as $accountId => $accountData)
    //             <tr class="divide-x border-light_blue bg-atlantis-200 level_1 revenue-row">
    //                 <x-ui.table-td padding="p-1 pl-4"
    //                                baseClass="text-dark_gray sticky left-0 z-10 bg-atlantis-200 whitespace-nowrap">
    //                     {{$accountData['name']}}
    //                 </x-ui.table-td>

    //                 @foreach($periodDates as $currentDate)
    //                     @php
    //                         $value = $accountData['total_db'][$currentDate] ?? 0;
    //                     @endphp
    //                     <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
    //                         <input
    //                             class="w-full px-2 py-1 text-right border-none bg-atlantis-200
    //                         can_apply_negative_class
    //                         @if ($value < 0) allocation-negative-value @endif "
    //                             type="text" pattern="[0-9]{10}"
    //                             value="{{ number_format($value, 0, '.', '')}}"
    //                             disabled/>
    //                     </x-ui.table-td>
    //                 @endforeach
    //             </tr>
    //         @endforeach
    //     @endif

    // @endif


    //start new line

    foreach ($this->exportCSV['tableData'] as $accountType => $accountsArray){
        if($accountType != BankAccount::ACCOUNT_TYPE_REVENUE){
            foreach ($accountsArray as $accountId => $accountData){
                foreach ($this->exportCSV['accountsSubTypes'] as $subType => $subTypeArray) {
                    if($subType == '_dates'){
                        $this->accountDataName[] = $accountData['name'];
                    }else{
                        $this->accountDataName[] = $subTypeArray['title'];
                    }

                    if (array_key_exists($subType, $accountData)){
                       foreach($this->exportCSV['periodDates'] as $cdateKey => $currentDate){
                            $value = $accountData[$subType][$currentDate] ?? 0;
                            $manual = $subType == '_dates' ? ($accountData['manual'][$currentDate] ?? null) : null;
                            $value= number_format($value, 0, '.', '');
                            $this->valueinput[$cdateKey] = $value;
                       }
                    }
                }
                if ($this->exportCSV['projectionMode'] == 'expense'){
                    if (array_key_exists('flows',$accountData)){
                        foreach ($accountData['flows'] as $flowId => $flowData){
                            $this->rowIndex++;
                            $columnIndex = 0;
                        }
                    }
                }
            }
        }
    }






//                 @if ($projectionMode == 'expense')

//                     @if (array_key_exists('flows',$accountData))
//                         @foreach ($accountData['flows'] as $flowId => $flowData)
//                             @php
//                                 $rowIndex++;
//                                 $columnIndex = 0;
//                             @endphp
//                             <tr data-account_id="{{$accountId}}"
//                                 class="divide-x bg-data-entry hover:bg-yellow-100 border-light_blue level_3">
//                                 <x-ui.table-td padding="p-1 pr-2 pl-4"
//                                                class="sticky left-0 z-10 text-left bg-data-entry whitespace-nowrap">
//                                     <div class="flex mr-auto">
//                                         <div
//                                             class="inline-flex
//                                                 text-{{$flowData['negative_flow'] ? 'red-500' : 'green' }}
//                                                 text-2xl pr-2 w-5 leading-none">
//                                             {!! $flowData['negative_flow'] ? '&ndash;' : '+' !!}
//                                         </div>
//                                         <div class="inline-flex flex-grow">
//                                             {{$flowData['label']}}
//                                         </div>
//                                         <div class="inline-flex px-4 text-right">
//                                             ({{$flowData['certainty']}}%)
//                                         </div>
//                                         <a onclick="Livewire.emit('openModal', 'modal-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId, 'routeName' => 'allocations-new']) }})"
//                                            title="Edit {{$flowData['label']}}"
//                                            class="opacity-50 cursor-pointer hover:opacity-100">
//                                             <x-icons.edit class="inline-flex self-end h-3 ml-auto"/>
//                                         </a>
//                                         <a onclick="Livewire.emit('openModal', 'modal-quick-entry-data-for-flow',  {{ json_encode(['accountId' => $accountId, 'flowId' => $flowId]) }})"
//                                            title="Quick Entry for Flow {{$flowData['label']}}"
//                                            class="ml-3 opacity-50 cursor-pointer hover:opacity-100">
//                                             <x-icons.fill class="inline-flex self-end h-3 ml-auto text-green"/>
//                                         </a>
//                                     </div>
//                                 </x-ui.table-td>
//                                 @foreach($periodDates as $currentDate)
//                                     @php
//                                         $columnIndex++;
//                                         //$currentDate = $date->format('Y-m-d');
//                                         $value = $flowData['_dates'][$currentDate] ?? 0;
//                                     @endphp
//                                     <x-ui.table-td padding="p-0" class="text-right hover:bg-yellow-200">
//                                         <input class="px-2 py-1 w-full text-right bg-transparent border-0
//                                             border-transparent outline-none
//                                             focus:outline-none focus:ring-1 focus:shadow-none disabled:opacity-90
//                                             @if(!$checkLicense) focus:bg-gray-100
//                                             @else pfp_copy_move_element hover:bg-yellow-50 focus:bg-yellow-50
//                                             @endif
//                                             "
//                                                @if($checkLicense) draggable="true" @endif

//                                                id="flow_{{$flowId}}_{{$currentDate}}"
//                                                type="text" pattern="[0-9]{10}"
//                                                data-row="{{$rowIndex}}"
//                                                data-column="{{$columnIndex}}"
//                                                data-certainty="{{$flowData['certainty']}}"
//                                                data-negative="{{$flowData['negative_flow']}}"
//                                                value="{{$value}}"
//                                                @if(!$checkLicense) disabled @endif/>
//                                     </x-ui.table-td>
//                                 @endforeach
//                             </tr>
//                         @endforeach
//                     @endif

//                 @endif

//             @endforeach
//         @endif

//         @if ($projectionMode == 'expense')
//             <tr class="bg-light_blue level_2">
//                 <x-ui.table-td class="h-1 text-center" padding="p-0"
//                                attr="colspan={{count($period)+1}}"></x-ui.table-td>
//             </tr>
//         @endif
//     @endforeach

//     @if ($projectionMode == 'forecast' && $business->flag == 1)
//     <tr class="total_row">
//                     <x-ui.table-td class="pl-5" padding="pl-0" attr="disabled"> 
//                                     <?php  echo "Total"; 


}
}