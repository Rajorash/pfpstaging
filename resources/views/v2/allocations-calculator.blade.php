<x-app-layout>
    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Allocations
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-slot name="subHeader">
        <div class="flex content-between">
            <input type="hidden" id="businessId" name="businessId" value="{{$business->id}}"/>
            <div class="py-2 pr-6">
                <label for="startdate">Start date</label>
                <input name="startdate" id="startDate" class="form-input rounded py-0 mx-3 my-0" type="date"
                       value="{{$startDate}}">
            </div>
            <div class="py-2 pr-6">
                <label for="range">Range</label>
                <select name="range" id="currentRangeValue" class="form-select rounded py-0 mx-3 my-0">
                    @foreach ($rangeArray as $key => $value)
                        <option value="{{$key}}" @if($key == $currentRangeValue) selected @endif>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot>

    <div id="allocationTablePlace" class="global_nice_scroll"></div>

    <x-spinner-block />

    <script type="text/javascript">
        window.allocationsControllerUpdate = "{{route('allocations-controller-update')}}";
    </script>
</x-app-layout>
