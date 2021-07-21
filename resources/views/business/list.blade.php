<x-app-layout>

    <x-slot name="header">
        {{ __('Businesses') }}
    </x-slot>

    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption>
                Businesses Visible To You

                @if($currentUser->isAdvisor())
                    <div class="text-xl pl-2">
                        Available seats: {{$currentUser->seats - count($currentUser->licenses)}} / {{$currentUser->seats}}
                    </div>
                @endif

                <x-slot name="right">
                    @livewire('business.create-business-form')
                </x-slot>
            </x-ui.table-caption>
            <thead>
            <tr class="border-light_blue border-t border-b">
                <x-ui.table-th padding="pl-12 pr-2 py-4">Business Name</x-ui.table-th>
                <x-ui.table-th>Owner</x-ui.table-th>
                <x-ui.table-th>License</x-ui.table-th>
                <x-ui.table-th class="text-center">Accounts</x-ui.table-th>
                @if($currentUser->isAdvisor())
                    <x-ui.table-th></x-ui.table-th>
                    <x-ui.table-th></x-ui.table-th>
                @endif
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th padding="pl-2 pr-12 py-4"></x-ui.table-th>
            </tr>
            </thead>

            <x-ui.table-tbody>
                @foreach ($businesses as $business)
                    <tr>
                        <x-ui.table-td class="whitespace-nowrap"
                                       padding="pl-12 pr-2 py-4">{{ $business->name }}</x-ui.table-td>
                        <x-ui.table-td>
                            <div class="flex items-center">
                                @if($business->owner)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                             src="{{ $business->owner->profile_photo_url }}"
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
                                @endif
                            </div>
                        </x-ui.table-td>
                        <x-ui.table-td>
                            @if ( is_object($business->license) )
                                {{$business->license->advisor->name}} @if($business->license->advisor->id == $currentUser->id)<span class="text-light_gray">(You)</span>@endif <br>                                <span class="text-sm text-light_gray">
                                    Acc Number: {{$business->license->account_number }}
                                </span>
                            @else
                                {{__('Not licensed')}}
                            @endif
                            @if ( is_object($business->collaboration)
                                )
                                <div class="text-sm text-light_gray">
                                    In collaboration with
                                    {{$business->collaboration->advisor->name}}
                                    @if (($expire = new \DateTime($business->collaboration->expires_at))->getTimestamp() > time())
                                        till {{$expire->format('Y-m-d')}}
                                    @endif
                                </div>
                            @endif
                        </x-ui.table-td>
                        <x-ui.table-td class="text-center">
                            <a href="{{url('/business/'.$business->id.'/accounts')}}">
                                <x-ui.badge> {{$business->accounts()->count()}}</x-ui.badge>
                            </a>
                        </x-ui.table-td>
                        @if(Auth::user()->isAdvisor())
                            <x-ui.table-td>
                                <x-ui.button-small href="{{route('maintenance.business', ['business' => $business])}}">
                                    Maintenance
                                </x-ui.button-small>
                            </x-ui.table-td>
                            <x-ui.table-td>
                                <x-ui.button-small href="{{route('licenses.business', ['business' => $business])}}">
                                    Licenses
                                </x-ui.button-small>
                            </x-ui.table-td>
                        @endif
                        <x-ui.table-td>
                            <x-ui.button-small href="{{route('allocations-percentages', ['business' => $business])}}">
                                Percentages
                            </x-ui.button-small>
                        </x-ui.table-td>
                        <x-ui.table-td>
                            <x-ui.button-small href="{{route('allocations-calendar', ['business' => $business])}}">Data
                                Entry
                            </x-ui.button-small>
                        </x-ui.table-td>
                        <x-ui.table-td padding="pl-2 pr-12 py-4">
                            <x-ui.button-small href="{{route('projections', ['business' => $business])}}">Forecast
                            </x-ui.button-small>
                        </x-ui.table-td>
                    </tr>
                @endforeach

            </x-ui.table-tbody>

        </x-ui.table-table>

    </x-ui.main>


</x-app-layout>
