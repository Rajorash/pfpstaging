@props([
'width' => 'max-w-7xl',
'heightController' => false
])
<div
    class="{{$width}} mx-auto py-1 px-4 sm:px-6 lg:px-8
    text-sm flex justify-end flex-nowrap items-baseline relative">
    @if($heightController)
        <div class="mr-8 flex flex-nowrap">
            <input type="radio" value="full" id="height_full" name="block_different_height" class="hidden radio_as_switcher"/>
            <label for="height_full" class="">Full height</label>
            <input type="radio" value="window" id="height_window" name="block_different_height" class="hidden radio_as_switcher"/>
            <label for="height_window" class="">Height by window</label>
        </div>
    @endif
    <div>
        <input type="number" min="1" max="30" id="delay_submit_data" class="py-0 px-1 text-center mr-1">
        <label for="delay_submit_data">delay (in sec) between submit data</label>
    </div>
</div>
