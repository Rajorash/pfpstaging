<x-app-layout>
    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Projections
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-slot name="subHeader">
        <div class="flex content-between">
            <input type="hidden" id="businessId" name="businessId" value="{{$business->id}}"/>
            <div class="py-2 pr-6">
                <label for="range">Range</label>
                <select name="range" id="currentProjectionsRange" class="form-select rounded py-1 mx-3 my-0">
                    @foreach ($rangeArray as $key => $value)
                        <option value="{{$key}}"
                                @if($key == $currentProjectionsRange) selected @endif>{{$value}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mr-4 py-2">
                <button id="recalculate_pf" class="text-center select-none border font-normal whitespace-no-wrap
           rounded-lg py-1 px-6 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2">{{__('Recalculate data for current period')}}</button>
            </div>
        </div>
    </x-slot>

    <x-ui.main width="w-full">
        <div id="projectionsTablePlace" class="global_nice_scroll return_coordinates_table">
            <div class="p-8 text-center opacity-50">...loading</div>
        </div>
    </x-ui.main>

    <x-spinner-block/>

    <script type="text/javascript">
        window.projectionsControllerUpdate = "{{route('projections-controller-update')}}";
    </script>

</x-app-layout>
