<x-app-layout>

    <x-slot name="titleHeader">
        {{$business->name}}
        &gt;
        {{__('Percentages')}}
    </x-slot>

    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        {{__('Percentages')}}
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-slot name="subHeader">
        <x-ui.data-submit-controls :projectionMode="true" :deepRecords="false"/>
    </x-slot>

    @if(!optional($business->license)->checkLicense)
        <div class="font-bold text-center text-red-500">{{__('License is inactive. Edit data forbidden.')}}</div>
    @endif

    <x-ui.main>
        <div id="percentagesTablePlace" class="global_nice_scroll return_coordinates_table">
            <div class="p-8 text-center opacity-50">...loading</div>
        </div>
    </x-ui.main>

    <x-spinner-block/>

    <script type="text/javascript">
        window.percentagesBusinessId = '{{$business->id}}';
        window.percentagesControllerUpdate = "{{route('allocations-percentages-update')}}";
    </script>

</x-app-layout>
