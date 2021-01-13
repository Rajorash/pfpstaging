@props([
    'tableId' => null
])

<div {{ $attributes->merge(['class' => "flex flex-col"]) }}>
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <table {{ $tableId ? "id=${tableId}" : '' }} class="min-w-full divide-y divide-gray-200">
                    {{ $slot }}
                </table>
            </div>
        </div>
    </div>
</div>
