<x-app-layout>

    <x-slot name="titleHeader">
        {{$business->name}}
        &gt;
        {{__('Projection Forecast')}}
    </x-slot>

    <x-slot name="header">
        <x-cta-workflow :business="$business" :step="'projections'"/>
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        {{__('Projection Forecast')}}
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-slot name="subHeader">
        <div class="flex content-between">
            <input type="hidden" id="businessId" name="businessId" value="{{$business->id}}"/>

            <div class="py-2 pr-2">
                <label for="range">{{__('Range')}}</label>
                <select name="range" id="currentProjectionsRange" class="form-select rounded py-1 mx-3 my-0">
                    @foreach ($rangeArray as $key => $value)
                        <option value="{{$key}}"
                                @if($key == $currentProjectionsRange) selected @endif>{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <div class="py-2 mr-4" style="display: none">
                <button id="prev_page" class="text-center select-none border font-normal whitespace-no-wrap
           rounded-lg py-1 px-6 pl-3 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2">
                    <x-icons.chevron-left :class="'mr-3 h-3 w-auto inline-block'"/>
                    <span class="place">{{__('prev')}}</span>
                </button>
            </div>

            <div class="py-2" style="display: none">
                <button id="next_page" class="text-center select-none border font-normal whitespace-no-wrap
           rounded-lg py-1 px-6 pr-3 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2">
                    <span class="place">{{__('next')}}</span>
                    <x-icons.chevron-right :class="'ml-3 h-3 w-auto inline-block'"/>
                </button>
            </div>
        </div>
    </x-slot>

    <x-ui.main width="w-full">
        <div id="projectionsTablePlace"
             class="relative overflow-scroll global_nice_scroll return_coordinates_table">
            <div class="p-8 text-center opacity-50">...loading</div>
        </div>
    </x-ui.main>

    <x-spinner-block/>

    <script type="text/javascript">
        window.projectionsControllerUpdate = "{{route('projections-controller-update')}}";
    </script>
</x-app-layout>
