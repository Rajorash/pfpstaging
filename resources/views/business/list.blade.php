<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Businesses') }}
        </h2>
    </x-slot>

    <div class="container py-3 mx-auto">
        {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> --}}
            <x-ui.card bodypadding="0">
                <x-slot name="header">
                    <h2 class="text-lg leading-6 font-medium text-black">Businesses Visible To You</h2>
                </x-slot>
                <x-ui.business-table :businesses="$businesses" />
            </x-ui.card>
        {{-- </div> --}}
    </div>


</x-app-layout>
