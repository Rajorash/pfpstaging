@props([
'width' => 'max-w-7xl',
'heightController' => false,
'forecastController' => false
])
<div class="{{$width}} mx-auto p-2 text-sm flex justify-end items-center">

    @if($forecastController)
        <div title="Enabling this allows a user to manually add forecast values by double clicking them." class="flex items-center mr-4 flex-nowrap">
            <input type="checkbox" id="allow_forecast_double_click" class="px-2 py-2 mx-3 my-3 rounded form-input"/>
            <label for="allow_forecast_double_click">{{__('Enable manual quick add')}}</label>
        </div>
    @endif

    @if($heightController)
        <div class="flex mr-4 flex-nowrap">
            <input type="radio" value="full" id="height_full" name="block_different_height"
                   class="hidden radio_as_switcher"/>
            <label for="height_full" class="">Full height</label>
            <input type="radio" value="window" id="height_window" name="block_different_height"
                   class="hidden radio_as_switcher"/>
            <label for="height_window" class="">Height by window</label>
        </div>
    @endif

    <div title="The amount of seconds that will pass before the form will automatically recalculate after updating values">
        <input type="number" min="1" max="30" id="delay_submit_data" class="w-16 px-2 py-0 py-1 my-0 ml-3 mr-0 text-center rounded form-input">
        <label class="ml-3" for="delay_submit_data">Auto submit delay (sec)</label>
    </div>

</div>
