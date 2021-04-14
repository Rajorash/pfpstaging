@props([
'width' => 'max-w-7xl'
])
<div
    class="{{$width}} mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="
        bg-white overflow-hidden border border-light_blue rounded-2xl">
        {{ $slot }}
    </div>
</div>
