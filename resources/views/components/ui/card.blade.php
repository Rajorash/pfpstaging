@props(['bodypadding' => 6])

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900 flex items-center justify-between">
                {{ $header }}
            </div>

            <div class="flex-auto p-{{$bodypadding}}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
