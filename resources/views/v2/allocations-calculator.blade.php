<x-app-layout>
    <x-slot name="header">
        <div class="flex content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$business->name}} > Allocations
            </h2>
            <x-business-nav businessId="{{$business->id}}"/>
        </div>
        <div class="flex content-between">
            <div class="py-2 pr-6">
                <label for="startdate">Start date</label>
                <input name="startdate" id="startDate" class="form-input rounded py-0 mx-3 my-0" type="date"
                       value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
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

    <div class="py-1"></div>

    <script type="text/javascript">
        window.allocationsControllerUpdate = "{{route('allocations-controller-update')}}";
    </script>
</x-app-layout>
