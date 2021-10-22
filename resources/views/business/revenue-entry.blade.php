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
            <input type="hidden" id="revenueBusinessId" name="revenueBusinessId" value="{{$business->id}}"/>
            <div class="p-2">
                <label class="mr-2" for="revenueStartDate">{{__('Start date')}}</label>
                <input name="revenueStartDate" id="revenueStartDate"
                       min="{{$minDate}}" max="{{$maxDate}}"
                       class="py-1 my-0 rounded form-input" type="date"
                       value="{{$startDate}}">
            </div>
            <div class="p-2">
                <label class="mr-2" for="revenueCurrentRangeValue">{{__('Range')}}</label>
                <select name="revenueCurrentRangeValue" id="revenueCurrentRangeValue" class="py-1 my-0 rounded form-select">
                    @foreach ($rangeArray as $key => $value)
                        <option value="{{$key}}" @if($key == $currentRangeValue) selected @endif>{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <x-ui.data-submit-controls class="items-center p-2" :heightController="false" :autoSubmit="false"/>
        </div>
    </x-slot>

    @if(!$business->license->checkLicense)
        <div class="font-bold text-center text-red-500">{{__('License is inactive. Edit data forbidden.')}}</div>
    @endif


    <x-ui.main width="w-full">

        <div id="revenueTablePlace"
             class="relative overflow-scroll global_nice_scroll block_different_height return_coordinates_table">
        </div>
    </x-ui.main>

    <x-spinner-block/>

    <script type="text/javascript">
        window.revenueControllerUpdate = "{{route('revenue-entry.loadData')}}";
        window.revenueControllerSave = "{{route('revenue-entry.saveData')}}";
    </script>

</x-app-layout>
