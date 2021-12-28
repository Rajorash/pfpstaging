<x-app-layout>

    <x-slot name="titleHeader">
        @isset($business)
            {{$business->name}}
            <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        @endisset
        {{ __('Allocations Calculator') }}
    </x-slot>

    <x-slot name="header">
        <x-cta-workflow :business="$business" :step="'allocation-calculator'" />
        @isset($business)
            {{$business->name}}
            <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        @endisset
        {{ __('Allocations Calculator') }}
    </x-slot>

    @if($business ?? false)
        <x-slot name="subMenu">
            <x-business-nav businessId="{{$business->id}}" :business="$business"/>
        </x-slot>
    @endif

    <x-ui.main>
        <livewire:allocation-calculator :businesses="$businesses" :businessId="$businessId"/>
    </x-ui.main>

</x-app-layout>
