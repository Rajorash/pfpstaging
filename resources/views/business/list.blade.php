<x-app-layout>

    <x-slot name="header">
        {{ __('Businesses') }}
    </x-slot>

    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption>
                Businesses Visible To You

                @if($currentUser->isAdvisor())
                    <div class="pl-2 text-xl">
                        Available seats: {{$currentUser->seats - count($currentUser->activeLicenses)}}
                        / {{$currentUser->seats}}
                    </div>
                @endif

                <x-slot name="right">
                    @livewire('business.create-business-form')
                </x-slot>
            </x-ui.table-caption>
            <thead>
            <tr class="border-t border-b border-light_blue">
                {{-- Business Column header row --}}
                <x-ui.table-th padding="pl-12 pr-2 py-4">Business Name</x-ui.table-th>
                {{-- Owner Column header row --}}
                <x-ui.table-th>Owner</x-ui.table-th>
                {{-- License Column header row --}}
                <x-ui.table-th>License</x-ui.table-th>
                {{-- Accounts Column header row --}}
                <x-ui.table-th class="text-center">Accounts</x-ui.table-th>
                @if($currentUser->isAdvisor())
                    {{-- Maintenance column header row --}}
                    <x-ui.table-th></x-ui.table-th>
                @endif
                {{-- Allocations Calculator column header row --}}
                <x-ui.table-th></x-ui.table-th>
                {{-- Percentages column header row --}}
                <x-ui.table-th></x-ui.table-th>
                {{-- Data Entry column header row --}}
                <x-ui.table-th></x-ui.table-th>
                {{-- Forecast column header row --}}
                <x-ui.table-th padding="pl-2 pr-12 py-4"></x-ui.table-th>
            </tr>
            </thead>

            <x-ui.table-tbody>
                @foreach ($businesses as $business)
                    <tr>
                        {{-- Business Column --}}
                        <x-ui.table-td padding="pl-12 pr-2 py-4">
                            {{ $business->name }}
                        </x-ui.table-td>
                        {{-- Owner Column --}}
                        <x-ui.table-td>
                            <div class="flex items-center">
                                @if($business->owner)
                                    <div class="flex-shrink-0 w-10 h-10">
                                        <img class="w-10 h-10 rounded-full"
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
                        {{-- License Column --}}
                        <x-ui.table-td>
                            @if ( is_object($business->license) )
                                {{$business->license->advisor->name}}
                                @if($business->license->advisor->id == $currentUser->id)
                                    <span class="text-light_gray">(You)</span>
                                @endif <br>
                                <span class="text-sm text-light_gray">
                                <span class="flex items-center whitespace-nowrap">
                                @if ($business->license->checkLicense)
                                        <x-icons.active :class="'h-4 w-auto text-green mr-1 align-text-bottom'"/>
                                    @else
                                        <x-icons.inactive :class="'h-4 w-auto text-gray-500 mr-1 align-text-bottom'"/>
                                    @endif
                                    <span>
                                        {{ $business->license->account_number }}
                                        @if( $currentUser->isAdvisor() )
                                            <a class="text-blue hover:text-dark_gray2"
                                               href="{{route('licenses.business', ['business' => $business])}}"
                                            >
                                        <x-icons.link class="inline w-auto h-4 mr-2 align-text-bottom"/>
                                        </a>
                                        @endif
                                    </span>
                                </span>
                            </span>
                            @else
                                {{__('Not licensed')}}
                            @endif
                            @if ( is_object($business->collaboration) && is_object($business->collaboration->advisor))
                                <div class="text-sm text-light_gray">
                                    @if($business->collaboration->advisor->user_id != auth()->user()->id)
                                        In collaboration with <b>{{$business->collaboration->advisor->user->name}}</b>
                                    @else
                                        @if(is_object($business->license))
                                            As collaborationist with <b>{{$business->license->advisor->name}}</b>
                                        @endif
                                    @endif

                                    @if (($expire = new \DateTime($business->collaboration->expires_at))->getTimestamp() > time())
                                        till {{$expire->format('Y-m-d')}}
                                    @endif
                                </div>
                            @endif
                        </x-ui.table-td>
                        {{-- Accounts Column --}}
                        <x-ui.table-td class="text-center">
                            <a href="{{url('/business/'.$business->id.'/accounts')}}">
                                <x-ui.badge> {{$business->accounts()->count()}}</x-ui.badge>
                            </a>
                        </x-ui.table-td>
                        @if(Auth::user()->isAdvisor())
                            {{-- Maintenance column --}}
                            <x-ui.table-td>
                                @if(
                                (is_object($business->collaboration)
                                && is_object($business->collaboration->advisor)
                                && $business->collaboration->advisor->user_id  != auth()->user()->id)
                                || !is_object($business->collaboration))
                                    <x-ui.button-small title="Maintenance"
                                                       href="{{route('maintenance.business', ['business' => $business])}}">
                                        <x-icons.gear :class="'h-4 w-auto my-0.5 inline-block'"/>
                                    </x-ui.button-small>
                                @endif
                            </x-ui.table-td>
                        @endif
                        {{-- Allocations Calculator column --}}
                        <x-ui.table-td>
                            <x-ui.button-small title="Rollout Percentages (Allocations)"
                                               href="{{route('allocation-calculator-with-id', ['business' => $business])}}">
                                <x-icons.calculator :class="'h-5 w-auto inline-block'"/>

                            </x-ui.button-small>
                        </x-ui.table-td>
                        {{-- Percentages column --}}
                        <x-ui.table-td>
                            <x-ui.button-small title="Rollout Percentages"
                                               href="{{route('allocations-percentages', ['business' => $business])}}">
                                <x-icons.percent :class="'h-5 w-auto inline-block'"/>
                            </x-ui.button-small>
                        </x-ui.table-td>
                        {{-- Data Entry column --}}
                        <x-ui.table-td>
                            <x-ui.button-small title="Data Entry" class="whitespace-nowrap"
                                               href="{{route('allocations-calendar', ['business' => $business])}}">
                                <x-icons.table :class="'h-5 w-auto inline-block'"/>
                            </x-ui.button-small>
                        </x-ui.table-td>
                        {{-- Forecast column --}}
                        <x-ui.table-td padding="pl-2 pr-12 py-4">
                            <x-ui.button-small title="Projection Forecast"
                                               href="{{route('projections', ['business' => $business])}}">
                                <x-icons.presentation-chart :class="'h-4 w-auto my-0.5 inline-block'"/>
                            </x-ui.button-small>
                        </x-ui.table-td>
                    </tr>
                @endforeach

            </x-ui.table-tbody>

        </x-ui.table-table>

    </x-ui.main>


</x-app-layout>
