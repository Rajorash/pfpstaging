@props([
'width' => 'max-w-7xl'
])
<div
    class="{{$width}} mx-auto py-1 px-4 sm:px-6 lg:px-8
    text-sm flex justify-end flex-nowrap items-baseline relative">
    <div>
        <input type="number" min="1" max="30" id="delay_submit_data" class="py-0 px-1 text-center mr-1">
        <label for="delay_submit_data">delay (in sec) between submit data</label>
    </div>
    <div id="delay_progress" class="absolute bg-gray-300 h-1 -bottom-2 right-4 sm:right-6 lg:right-8"></div>
</div>
