@props([
'width' => 'max-w-7xl',
'heightController' => false,
'forecastController' => false,
'autoSubmit' => true,
'projectionMode' => false,
])
<div class="{{$width}} ml-auto py-2 text-sm flex justify-end items-center">

    {{-- @if($forecastController)
        <div title="{{__('Enabling this allows a user to manually add forecast values by double clicking them.')}}"
             class="flex items-center mr-4 flex-nowrap">
            <input type="checkbox" id="allow_forecast_double_click" class="py-2 pr-2 mx-3 my-3 rounded form-input"/>
            <label for="allow_forecast_double_click">{{__('Enable manual quick add')}}
                <x-icons.question/>
            </label>
        </div>
    @endif --}}

    @if($heightController)
        <div class="flex flex-nowrap">
            <input type="radio" value="full" id="height_full" name="block_different_height"
                   class="hidden radio_as_switcher"/>
            <label for="height_full" class="text-right whitespace-nowrap h-9"
                   title="{{__('Full height')}}">{{__('Full')}}</label>
            <input type="radio" value="window" id="height_window" name="block_different_height"
                   class="hidden radio_as_switcher"/>
            <label for="height_window" class="whitespace-nowrap h-9"
                   title="{{__('Height by window')}}">{{__('Window')}}</label>
        </div>
    @endif


    @if($autoSubmit)
        <div class="flex items-center ml-4"
             title="{{__('The amount of seconds that will pass before the form will automatically recalculate after updating values')}}">
            <input type="number" min="1" max="30" id="delay_submit_data"
                   class="w-16 mx-3 my-0 text-center text-right rounded h-9 form-input">
            <label class="inline-flex space-x-4" for="delay_submit_data">
                <span>{{__('Auto submit delay (sec)')}}</span>
                <x-icons.question/>
            </label>
        </div>

        <div title="{{__('Enabling auto-submit data. If not - use Enter for submit')}}"
             class="flex items-center ml-3">
            <input type="checkbox" id="allow_auto_submit_data" class="py-2 mx-3 my-3 rounded form-input"/>
            <label class="inline-flex space-x-4" for="allow_auto_submit_data">
                <span>{{__('Auto-submit')}}</span>
                <x-icons.question/>
            </label>
        </div>
    @endif

    @unless($projectionMode)
        <div
            title="{{__('How deep show table. 1: Accounts Only; 2: + Transfer In & Flow total; 3: + Flows (Show all)')}}"
            class="flex items-center ml-3">
            <input type="range" id="show_rows_level" list="step_levels"
                   class="py-2 mx-3 my-3 rounded form-input" min="1" max="3" value="3"/>
            <label class="inline-flex justify-between w-48 space-x-4" for="show_rows_level">
                <span>{{__('Detailed view')}}</span>
                <x-icons.question/>
            </label>
            <datalist id="step_levels">
                <option value="1" label="1">{{__('Accounts Only')}}</option>
                <option value="2" label="2">{{__('+ Transfer In & Flow total')}}</option>
                <option value="3" label="all">{{__('+ Flows (Show all)')}}</option>
            </datalist>
        </div>
    @endunless
</div>
