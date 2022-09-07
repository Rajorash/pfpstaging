<x-app-layout>

    <x-slot name="titleHeader">
        {{$business->name}}
        &gt;
        {{__('Expense Entry')}}
    </x-slot>

    <x-slot name="header">
        <x-cta-workflow :business="$business" :step="'allocations'"/>
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        {{__('Expense Entry')}}
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

            <x-ui.data-submit-controls class="items-center p-2"
                                       :heightController="true" :autoSubmit="false" />

        </div>
    </x-slot>

    @php
        $checkLicense = $business->license->checkLicense;
    @endphp

    @if(!$checkLicense)
        <div class="font-bold text-center text-red-500">{{__('License is inactive. Edit data forbidden.')}}</div>
    @endif

    <div class= "pl-12 text-xl" >
        Last Date For Updated Bank Account Balance : <b> {{$updated_today}} </b>
    </div>

    <x-ui.main width="w-full">
        <div id="allocationsNewTablePlace"
             class="relative overflow-scroll global_nice_scroll block_different_height return_coordinates_table">
            <div class="p-8 text-center opacity-50">...loading</div>
        </div>
    </x-ui.main>

    <x-spinner-block/>

    <script type="text/javascript">
        window.allocationsNewControllerUpdate = "{{route('allocations-new-update')}}";
    </script>
</x-app-layout>
