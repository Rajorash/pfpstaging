<x-app-layout>

    <x-slot name="header">
        {{ __('Businesses') }}
    </x-slot>

    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption>Businesses Visible To You</x-ui.table-caption>
            <thead>
            <tr class="border-light_blue border-t border-b">
                <x-ui.table-th padding="pl-12 pr-2 py-4">Business Name</x-ui.table-th>
                <x-ui.table-th>Owner</x-ui.table-th>
                <x-ui.table-th>Advisor</x-ui.table-th>
                <x-ui.table-th class="text-center">Accounts</x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
            </tr>
            </thead>

            <x-ui.table-tbody>
                @foreach ($businesses as $business)
                    <tr>
                        <x-ui.table-td class="whitespace-nowrap"
                                       padding="pl-12 pr-2 py-4">{{ $business->name }}</x-ui.table-td>
                        <x-ui.table-td>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $business->owner->profile_photo_url }}"
                                         alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="">
                                        {{ $business->owner->name }}
                                    </div>
                                    <div class="text-sm text-light_gray">
                                        {{ $business->owner->email }}
                                    </div>
                                </div>
                            </div>
                        </x-ui.table-td>
                        <x-ui.table-td>{{is_object($business->license) ? $business->license->advisor->name : __('Not licensed')}}</x-ui.table-td>
                        <x-ui.table-td class="text-center">
                            <a href="{{url('/business/'.$business->id.'/accounts')}}">
                                <x-ui.badge> {{$business->accounts()->count()}}</x-ui.badge>
                            </a>
                        </x-ui.table-td>
                        <x-ui.table-td>
                            <x-ui.button-small href="{{route('allocations-percentages', ['business' => $business])}}">Percentages</x-ui.button-small>
                        </x-ui.table-td>
                        <x-ui.table-td>
                            <x-ui.button-small href="{{route('allocations-calendar', ['business' => $business])}}">Data Entry</x-ui.button-small>
                        </x-ui.table-td>
                        <x-ui.table-td>
                            <x-ui.button-small href="{{route('projections', ['business' => $business])}}">Forecast</x-ui.button-small>
                        </x-ui.table-td>
                    </tr>
                @endforeach

            </x-ui.table-tbody>

        </x-ui.table-table>

    </x-ui.main>

</x-app-layout>
