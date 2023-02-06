<x-app-layout>

    <x-slot name="titleHeader">
        @isset($business)
            {{$business->name}}
            <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        @endisset
        {{ __('Allocations Calculator') }}
    </x-slot>

    <x-slot name="header">
        <x-cta-workflow :business="$business" :step="'allocation-calculator'" />
        @isset($business)
            {{$business->name}}
            <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        @endisset
        {{ __('Allocations Calculator') }}
    </x-slot>

    @if($business ?? false)
        <x-slot name="subMenu">
            <x-business-nav businessId="{{$business->id}}" :business="$business"/>
        </x-slot>
    @endif


    <div class="p-6 sm:px-20">
    <div class="mb-4 text-lg text-center">{{__('Allocation Calculator For')}} {{$business->name}}
        <select class="px-3 py-1 pr-8 ml-4 rounded form-select" name="" id="" wire:model="selectedBusinessId">
            @foreach ($businesses as $business_list)
                <option value="{{$business_list->id}}"  @if($business_list->id == $business->id) selected  @endif>{{$business_list->name}}</option>
            @endforeach
        </select>
    </div>
    @if ($checkPercentagesSet == false)
        <div class="p-4 text-red-600 bg-red-100 border border-red-700 rounded-sm">
            It appears that the percentage values have not been set yet for this phase. You may go to the <a class="underline transition duration-500 ease-in-out hover:text-red-900" href="{{route('allocations-percentages', ['business' => $business])}}">percentages page</a> to set them.
        </div>
    @endif
    <x-ui.table >
        <thead>
            <tr>
                <x-ui.th></x-ui.th>
                <x-ui.th></x-ui.th>
                <x-ui.th class="w-10">{{__('Actual')}}</x-ui.th>
                <x-ui.th>{{__('Roll Out %')}}</x-ui.th>
                <x-ui.th>{{__('Allocation $')}}</x-ui.th>
            </tr>
        </thead>
        <tbody id="appendCal">
            {{-- Revenue account/s - should only && always be one? --}}
            @if (array_key_exists('revenue', $mappedAccounts))
                @foreach ($mappedAccounts['revenue'] as $account)
                <tr>
                    <td class="px-2 py-1 border border-gray-300">{{__('Top line revenue - Account')}} "{{$account['name']}}"</td>
                    <td class="px-2 py-1 border border-gray-300"></td>
                    <td class="w-32 px-2 py-1 bg-yellow-100 border border-gray-300">
                        <input type="text" autocomplete = "off" class= 'form-input text-right w-full p-2 mb-1 text-base leading-normal border-gray-200 rounded h-6'  name="revenue" onkeypress="return /^-?[0-9]*$/.test(this.value+event.key)" id="revenueinput" />
                        <input type="text" id="tb2" class="hidden" />
                    </td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                </tr>
                @endforeach
            @endif
            {{-- Sales tax account/s --}}
            @if (!$hideSalesTaxRows)
            <div class = "salestax-gs">
                @foreach ($mappedAccounts['salestax'] as $account)
                <tr >
                    <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                    <td class="px-2 py-1 text-right bg-indigo-200 border border-gray-300">{{$account['percent']}}%</td>
                    <td class="px-2 py-1 text-right bg-green-300 border border-gray-300">
                        ${{number_format($account['value'], 0)}}</td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                </tr>
                @endforeach

                {{-- Net Cash Receipts --}}
                <tr class="">
                    <td class="px-2 py-1 border border-gray-300">{{__('Net Cash Receipts')}}</td>
                    <td class="px-2 py-1 border border-gray-300"></td>
                    <td class="px-2 py-1 text-right border border-gray-300">${{number_format($netCashReceipts, 0)}}</td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                </tr>
            </div>
            @endif

            {{-- Pre-real account/s --}}
            @if (array_key_exists('prereal', $mappedAccounts))
                @foreach ($mappedAccounts['prereal'] as $account)
                <tr>
                    <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                    <td class="px-2 py-1 text-right bg-indigo-200 border border-gray-300">{{$account['percent']}}%</td>
                    <td class="px-2 py-1 text-right bg-green-100 border border-gray-300">
                        ${{number_format($account['value'], 0)}}</td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                    <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                </tr>
                @endforeach
            @endif

            {{-- Real Revenue --}}
            <tr class="bg-gray-200">
                <td class="px-2 py-1 border border-gray-300">{{__('Real Revenue')}}</td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 text-right bg-green-100 border border-gray-300 ">
                    ${{number_format($realRevenue, 0)}}</td>
                <td class="text-right px-2 py-1 border border-gray-300 {!! ($postrealPercentageSum > 100 || $postrealPercentageSum < 100) ? 'text-red-500' : '';!!}">{{$postrealPercentageSum}}
                    %
                </td>
                <td class="px-2 py-1 border border-gray-300"></td>
            </tr>

            {{-- Post-real accounts --}}
            @if (array_key_exists('postreal', $mappedAccounts))
                @foreach ($mappedAccounts['postreal'] as $account)
                <tr>
                    <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                    <td class="px-2 py-1 border border-gray-300"></td>
                    <td class="py-1 border border-gray-300 bg-green-100px-2"></td>
                    <td class="px-2 py-1 text-right bg-gray-400 border border-gray-300">{{$account['percent']}}%</td>
                    <td class="px-2 py-1 text-right bg-green-200 border border-gray-300">
                        ${{number_format($account['value'], 0)}}</td>
                </tr>
                @endforeach
            @endif

            {{-- Check sum --}}
            <tr class="bg-gray-200">
                <td class="px-2 py-1 border border-gray-300">
                    {{__('Allocation Sum')}}
                </td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 text-right border border-gray-300">
                    ${{number_format($allocationSum, 2)}}
            </tr>
            <tr class="bg-gray-200">
                <td class="px-2 py-1 border border-gray-300">
                    {{__('Error Check')}}
                </td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 text-right border border-gray-300">
                    <span
                        class="{!! round($checksum, 2) == 0 ? '' : 'text-red-500';!!}">${{number_format($checksum, 2)}}</span>
            </tr>
        </tbody>
    </x-ui.table>
</div>

<script>


// $(document).ready(function(){

    var enterTrue = true;

    $(document).on('blur', '#revenueinput' ,function(e){
        if(enterTrue){
            calculationAppend();
        }
        enterTrue = true;
    })

    $(document).on('keydown', '#revenueinput' ,function(e){
        if (e.keyCode == 13) {
            calculationAppend();
            enterTrue =  false;
        }
    })


    function calculationAppend() {
        var revenueinput = $('#revenueinput').val();

        // alert(revenueinput);

        if(revenueinput !== ''){

            var businessId="{{$business->id}}";
            
            $.ajax({
                url: "{{ url('/calculating') }}", 
                type: "POST",
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify({ revenueinput: revenueinput, businessId: businessId }),
                success: function (result) {
                        if(result.return){
                            // console.log(result,"working hai bhaiii");
                            $('#appendCal').html('');
                
                                if('revenue' in result.mappedAccounts) { 
                                for(let i = 0; i < result.mappedAccounts.revenue.length; i++){ 
                                    var appendtable  = "<tr><td class='px-2 py-1 border border-gray-300'>{{__('Top line revenue - Account')}} "+result.mappedAccounts.revenue[i].name+"</td><td class='px-2 py-1 border border-gray-300'></td><td class='w-32 px-2 py-1 bg-yellow-100 border border-gray-300'><input type='text' value='"+revenueinput+"' class= 'form-input text-right w-full p-2 mb-1 text-base leading-normal border-gray-200 rounded h-6' name='revenue' autocomplete = 'off'  onkeypress='return /^-?[0-9]*$/.test(this.value+event.key)' id='revenueinput' /></td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td></tr>"
                                }} 
                                if (!result.hideSalesTaxRows){
                                for(let i = 0; i < result.mappedAccounts.salestax.length; i++){ 

                                appendtable  +=    "<tr><td class='px-2 py-1 border border-gray-300'>"
                                    +result.mappedAccounts.salestax[i].name+
                                    "</td><td class='px-2 py-1 text-right bg-indigo-200 border border-gray-300'>"
                                    + result.mappedAccounts.salestax[i].percent+ 
                                    "%</td><td class='px-2 py-1 text-right bg-green-300 border border-gray-300'>$"
                                        + Math.round(result.mappedAccounts.salestax[i].value) + 
                                        "</td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td></tr>"
                            } 
                            appendtable  +=          "<tr><td class='px-2 py-1 border border-gray-300'>{{__('Net Cash Receipts')}}</td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 text-right border border-gray-300'>$"
                                        +Math.round(result.netCashReceipts)+
                                        "</td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td></tr>"
                        } if('prereal' in result.mappedAccounts) {  
                            for(let i = 0; i < result.mappedAccounts.prereal.length; i++){ 
                                appendtable  +=      "<tr><td class='px-2 py-1 border border-gray-300'>"
                            +result.mappedAccounts.prereal[i].name+
                            "</td><td class='px-2 py-1 text-right bg-indigo-200 border border-gray-300'>"
                            +result.mappedAccounts.prereal[i].percent+
                            "%</td><td class='px-2 py-1 text-right bg-green-100 border border-gray-300'>$"
                            + Math.round(result.mappedAccounts.prereal[i].value) + 
                            "</td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td><td class='px-2 py-1 bg-gray-200 border border-gray-300'></td></tr>"
                    }}

                    appendtable  +=     "<tr class='bg-gray-200'><td class='px-2 py-1 border border-gray-300'>{{__('Real Revenue')}}</td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 text-right bg-green-100 border border-gray-300 '>$"
                        +Math.round(result.realRevenue)+
                        "</td><td class='text-right px-2 py-1 border border-gray-300"; 
                        if(result.postrealPercentageSum > 100 || result.postrealPercentageSum < 100) {
                            appendtable  +=  "text-red-500";
                        } else{
                            appendtable  +=  "";
                        }
                        appendtable  += "'>";
                        
                        appendtable  +=  result.postrealPercentageSum + "%</td><td class='px-2 py-1 border border-gray-300'></td></tr>";

                        if('postreal' in result.mappedAccounts) { 
                                        for(let i = 0; i < result.mappedAccounts.postreal.length; i++){ 
                
                                            appendtable  +=     "<tr><td class='px-2 py-1 border border-gray-300'>"
                        +result.mappedAccounts.postreal[i].name+
                        "</td><td class='px-2 py-1 border border-gray-300'></td><td class='py-1 border border-gray-300 bg-green-100px-2'></td><td class='px-2 py-1 text-right bg-gray-400 border border-gray-300'>"
                        +result.mappedAccounts.postreal[i].percent+
                        "%</td><td class='px-2 py-1 text-right bg-green-200 border border-gray-300'>$"
                        +Math.round(result.mappedAccounts.postreal[i].value)+
                                "</td></tr>"
                        }}

                        appendtable  +=  "<tr class='bg-gray-200'><td class='px-2 py-1 border border-gray-300'>{{__('Allocation Sum')}}</td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 text-right border border-gray-300'>$"
                        +Math.round(result.allocationSum)+
                        "</td></tr><tr class='bg-gray-200'><td class='px-2 py-1 border border-gray-300'>{{__('Error Check')}}</td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 border border-gray-300'></td><td class='px-2 py-1 text-right border border-gray-300'><span class='";
                        if(Math.round(result.checksum)  == 0 ){
                            appendtable += "";
                        }else{
                            appendtable +=  'text-red-500'; 
                        } 
                        appendtable += "'>$"
                        +Math.round(result.checksum)+
                        "</span></td></tr>";

                        $('#appendCal').html(appendtable);
                        
                        }
                    },
                    error: function (err) {
                        console.log(err,"error");
                    }
            });
        }
    }
// })

</script>


</x-app-layout>
