<x-app-layout>

    <x-slot name="$titleHeader">
        {{ __('Businesses') }}
    </x-slot>

    <x-slot name="header">
        {{ __('Businesses') }}
    </x-slot>

    <x-ui.main>

        @if (session('status'))
            <div
                class="p-3 mx-12 mt-8 text-base text-indigo-500 bg-indigo-100 border border-indigo-300 rounded-lg status">
                {{ session('status') }}
            </div>
        @endif

        <x-ui.table-table>
            <x-ui.table-caption>
                {{__('Businesses Visible To You')}}

                @if($currentUser->isAdvisor())
                    <div class="pl-2 text-xl">
                        {{__('Available seats:')}} {{$currentUser->seats - count($currentUser->activeLicenses)}}
                        / {{$currentUser->seats}}
                    </div>
                @endif

                
                <x-slot name="right">
                <div class="flex justify-center">
                    <div class="mb-3 xl:w-96">
                        <div class="input-group relative flex flex-wrap items-stretch w-full mb-4 rounded">
                                <input type="text" id="search" autocomplete="off" style="text-align: center;
    padding: 9px;" class="form-control relative flex-auto min-w-0 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
                        </div>
                    </div>
                </div>
                    @livewire('business.create-business-form')
                </x-slot>
            </x-ui.table-caption>
            <thead>
            <tr class="border-t border-b border-light_blue">
                {{-- Business Column header row --}}
                <x-ui.table-th padding="pl-12 pr-2 py-4">{{__('Business Name')}}</x-ui.table-th>
                {{-- Owner Column header row --}}
                <x-ui.table-th>Owner</x-ui.table-th>
                {{-- License Column header row --}}
                <x-ui.table-th>License</x-ui.table-th>
                {{-- Accounts Column header row --}}
                <x-ui.table-th class="text-center">{{__('Accounts')}}</x-ui.table-th>
                <x-ui.table-th padding="pl-2 pr-8">
                    {{-- Maintenance column header row --}}
                    {{-- Accounts --}}
                    {{-- Pipelines column header row --}}
                </x-ui.table-th>

                <x-ui.table-th padding="pl-2 pr-12 py-4">
                    {{-- Allocations Calculator column header row --}}
                    {{-- Account balance change manually --}}
                    {{-- Revenue Entry column header row --}}
                    {{-- Expense Entry column header row --}}
                    {{-- Forecast column header row --}}
                    {{-- Percentages column header row --}}
                </x-ui.table-th>
            </tr>
            </thead>

            <x-ui.table-tbody>
            @foreach ($businesses as $business)
                @if($business->license->advisor_id == $currentUser->id && $currentUser->isAdvisor())
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
                                    <span class="text-light_gray">{{__('(You)')}}</span>
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
                                        {{__('In collaboration with')}}
                                        <b>{{$business->collaboration->advisor->user->name}}</b>
                                    @else
                                        @if(is_object($business->license))
                                            {{__('As collaborationist with')}}
                                            <b>{{$business->license->advisor->name}}</b>
                                        @endif
                                    @endif

                                    @if (($expire = new \DateTime($business->collaboration->expires_at))->getTimestamp() > time())
                                        {{__('till')}} {{$expire->format('Y-m-d')}}
                                    @endif
                                </div>
                            @endif
                        </x-ui.table-td>

                        {{-- Accounts Column --}}
                        <x-ui.table-td class="text-center">
                            <a href="{{url('/business/'.$business->id.'/accounts')}}">
                                <x-ui.badge>{{$business->accounts_count}}</x-ui.badge>
                            </a>
                        </x-ui.table-td>

                        <x-ui.table-td class="pl-4 pr-8">
                            <div class="flex space-x-1">
                                {{-- Maintenance column --}}
                                @if(Auth::user()->isAdvisor())
                                        <x-ui.button-small title="Maintenance"
                                                            href="{{route('maintenance.business', ['business' => $business])}}">
                                            <x-icons.gear :class="'h-5 w-auto inline-block'"/>
                                        </x-ui.button-small>
                                @endif

                                {{-- Accounts --}}
                                <x-ui.button-small title="{{__('Accounts')}}"
                                    href="{{url('/business/'.$business->id.'/accounts')}}">
                                    <x-icons.vallet :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>


                                {{-- Percentages column --}}
                                <x-ui.button-small title="{{__('Rollout Percentages')}}"
                                                href="{{route('allocations-percentages', ['business' => $business])}}">
                                    <x-icons.percent :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                            </div>
                        </x-ui.table-td>


                        <x-ui.table-td padding="pl-2 pr-12 py-4">
                            <div class="flex space-x-1">

                                {{-- Allocations Calculator column --}}
                                <x-ui.button-small title="{{__('Allocations Calculator')}}"
                                                href="{{route('allocation-calculator-with-id', ['business' => $business])}}">
                                    <x-icons.calculator :class="'h-5 w-auto inline-block'"/>

                                </x-ui.button-small>

                                {{-- Manually change balances --}}
                                <x-ui.button-small title="{{__('Manually change balances')}}"
                                                href="{{url('/business/'.$business->id.'/balance')}}">
                                    <x-icons.balance :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                                {{-- Revenue Entry column --}}
                                <x-ui.button-small title="Revenue Entry" class="whitespace-nowrap"
                                                href="{{route('revenue-entry.table', ['business' => $business])}}">
                                    <x-icons.dollar-fill :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                                {{-- Expense Entry column --}}
                                <x-ui.button-small title="Expense Entry" class="whitespace-nowrap"
                                                href="{{route('allocations-new', ['business' => $business])}}">
                                    <x-icons.table :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                                {{-- Forecast column --}}
                                <x-ui.button-small title="{{__('Projection Forecast')}}"
                                                href="{{route('projection-view', ['business' => $business])}}">
                                    <x-icons.presentation-chart :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                            </div>

                        </x-ui.table-td>
                    </tr>
                    @elseif(!$currentUser->isAdvisor())
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
                                    <span class="text-light_gray">{{__('(You)')}}</span>
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
                                        {{__('In collaboration with')}}
                                        <b>{{$business->collaboration->advisor->user->name}}</b>
                                    @else
                                        @if(is_object($business->license))
                                            {{__('As collaborationist with')}}
                                            <b>{{$business->license->advisor->name}}</b>
                                        @endif
                                    @endif

                                    @if (($expire = new \DateTime($business->collaboration->expires_at))->getTimestamp() > time())
                                        {{__('till')}} {{$expire->format('Y-m-d')}}
                                    @endif
                                </div>
                            @endif
                        </x-ui.table-td>

                        {{-- Accounts Column --}}
                        <x-ui.table-td class="text-center">
                            <a href="{{url('/business/'.$business->id.'/accounts')}}">
                                <x-ui.badge>{{$business->accounts_count}}</x-ui.badge>
                            </a>
                        </x-ui.table-td>

                        <x-ui.table-td class="pl-4 pr-8">
                            <div class="flex space-x-1">
                                {{-- Maintenance column --}}
                                @if(Auth::user()->isAdvisor())
                                    @if(
                                    (is_object($business->collaboration)
                                    && is_object($business->collaboration->advisor)
                                    && ($business->collaboration->advisor->user_id != auth()->user()->id))
                                    || !is_object($business->collaboration))
                                        <x-ui.button-small title="Maintenance"
                                                            href="{{route('maintenance.business', ['business' => $business])}}">
                                            <x-icons.gear :class="'h-5 w-auto inline-block'"/>
                                        </x-ui.button-small>
                                    @endif
                                @endif

                                {{-- Accounts --}}
                                <x-ui.button-small title="{{__('Accounts')}}"
                                    href="{{url('/business/'.$business->id.'/accounts')}}">
                                    <x-icons.vallet :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>


                                {{-- Percentages column --}}
                                <x-ui.button-small title="{{__('Rollout Percentages')}}"
                                                href="{{route('allocations-percentages', ['business' => $business])}}">
                                    <x-icons.percent :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                            </div>
                        </x-ui.table-td>


                        <x-ui.table-td padding="pl-2 pr-12 py-4">
                            <div class="flex space-x-1">

                                {{-- Allocations Calculator column --}}
                                <x-ui.button-small title="{{__('Allocations Calculator')}}"
                                                href="{{route('allocation-calculator-with-id', ['business' => $business])}}">
                                    <x-icons.calculator :class="'h-5 w-auto inline-block'"/>

                                </x-ui.button-small>

                                {{-- Manually change balances --}}
                                <x-ui.button-small title="{{__('Manually change balances')}}"
                                                href="{{url('/business/'.$business->id.'/balance')}}">
                                    <x-icons.balance :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                                {{-- Revenue Entry column --}}
                                <x-ui.button-small title="Revenue Entry" class="whitespace-nowrap"
                                                href="{{route('revenue-entry.table', ['business' => $business])}}">
                                    <x-icons.dollar-fill :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                                {{-- Expense Entry column --}}
                                <x-ui.button-small title="Expense Entry" class="whitespace-nowrap"
                                                href="{{route('allocations-new', ['business' => $business])}}">
                                    <x-icons.table :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                                {{-- Forecast column --}}
                                <x-ui.button-small title="{{__('Projection Forecast')}}"
                                                href="{{route('projection-view', ['business' => $business])}}">
                                    <x-icons.presentation-chart :class="'h-5 w-auto inline-block'"/>
                                </x-ui.button-small>

                            </div>

                        </x-ui.table-td>
                    </tr>
                    @endif
                @endforeach

            </x-ui.table-tbody>

        </x-ui.table-table>

    </x-ui.main>

</x-app-layout>
<script>
    $(document).ready(function(){
            var rows = $('tbody tr');
            $(document).on('keyup keypress blur change click','#search',function() {
        // console.log(rows,"cc");

                var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

                rows.show().filter(function() {
                    var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                    return !~text.indexOf(val);
                }).hide();
            });
});
    </script>