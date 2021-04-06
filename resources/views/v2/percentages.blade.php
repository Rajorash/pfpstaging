<x-app-layout>
    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Percentages
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <div id="percentagesTablePlace" class="global_nice_scroll"></div>

    <x-spinner-block/>

    <script type="text/javascript">
        window.percentagesBusinessId = '{{$business->id}}';
        window.percentagesControllerUpdate = "{{route('allocations-percentages-update')}}";
    </script>

</x-app-layout>
