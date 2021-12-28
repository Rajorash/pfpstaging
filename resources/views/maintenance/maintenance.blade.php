<x-app-layout>

    <x-slot name="titleHeader">
        {{ __('Maintenance') }}
    </x-slot>

    <x-slot name="header">
        {{ __('Maintenance') }}
    </x-slot>

    <x-ui.main>
        @if (Auth::user()->isSuperAdmin())
            <livewire:maintenance :code="$code"/>
        @else
            <x-ui.error>
                <div class="py-10 text-center">
                    {{__('Sorry, this page only for Super Admin')}}
                </div>
            </x-ui.error>
        @endif
    </x-ui.main>

</x-app-layout>
