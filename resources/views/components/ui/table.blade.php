@props([
'tableId' => null
])

<div {{ $attributes->merge(['class' => "block w-full"]) }}>
    <div class="block w-full">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="block w-full">
                <table
                    {{ $tableId ? "id=${tableId}" : '' }} class="min-w-full table_hover_rows_cols border-collapse rounded-xl bg-white w-full text-dark_gray2">
                    {{ $slot }}
                </table>
            </div>
        </div>
    </div>
</div>
