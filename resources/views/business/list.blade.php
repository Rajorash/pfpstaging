<x-app-layout>

    <x-slot name="header">
        {{ __('Businesses') }}
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card bodypadding="0">
                <x-slot name="header">
                    <h2 class="text-2xl leading-6 font-normal">Businesses Visible To You</h2>
                </x-slot>
                <x-ui.business-table :businesses="$businesses"/>
            </x-ui.card>
        </div>
    </div>


</x-app-layout>
