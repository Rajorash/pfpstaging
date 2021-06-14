@props(['bodypadding' => 6])

<div class="bg-white overflow-hidden border border-light_blue sm:rounded-lg">
    <div class="pt-8 pb-4 px-6 mb-0 bg-white border-b-1 border-gray-300 text-dark_gray2 flex items-center justify-between">
        {{ $header }}
    </div>

    <div class="flex-auto">
        {{ $slot }}
    </div>
</div>

