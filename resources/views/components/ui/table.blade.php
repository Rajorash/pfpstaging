@props([
    'tableId' => null
])

<div {{ $attributes->merge(['class' => "block w-full overflow-x-auto"]) }}>
    <div class="block w-full">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="block w-full">
                <table {{ $tableId ? "id=${tableId}" : '' }} class="min-w-full divide-y divide-gray-200">
                    {{ $slot }}
                </table>
            </div>
        </div>
    </div>
</div>
