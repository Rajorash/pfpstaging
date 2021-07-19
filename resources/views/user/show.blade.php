<x-app-layout>
    <x-slot name="header">
        Users
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        User Details
    </x-slot>

    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-72 relative">
                User Details

                <x-slot name="left">
                    <div class="absolute left-12 top-12">
                        <x-ui.button-normal href="{{route('users')}}">
                            <x-icons.chevron-left :class="'h-3 w-auto'"/>
                            <span class="ml-2">Go back</span>
                        </x-ui.button-normal>
                    </div>
                </x-slot>

            </x-ui.table-caption>
            <x-ui.table-tbody>
                <tr>
                    <x-ui.table-td class="text-center bg-gray-100" padding="px-72 py-4">
                        <div class="table w-full">
                            <div class="table-row">
                                <div class="table-cell w-1/3 text-left align-top">
                                    <img class="h-36 w-36 rounded-full" src="{{ $user->profile_photo_url }}" alt="">
                                </div>
                                <div class="table-cell w-2/3 text-left">
                                    <div class="table w-full float-left">
                                        <div class="table-row">
                                            <div class="table-cell pb-2 w-1/3">{{__('Name')}}</div>
                                            <div class="table-cell pb-2 w-2/3">{{ $user->name }}</div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Role/s')}}</div>
                                            <div
                                                class="table-cell pb-2">{{ implode(', ',$user->roles->pluck('label')->toArray()) }}</div>
                                        </div>

                                        @if(Auth::user()->isSuperAdmin() && $user->isRegionalAdmin())
                                            <div class="table-row">
                                                <div class="table-cell pb-2">{{__('Advisors:')}}</div>
                                                <div class="table-cell pb-2">
                                                    @if($user->advisorsByRegionalAdmin)
                                                        <ol class="list-disc">
                                                            @foreach ($user->advisorsByRegionalAdmin as $advisor_row)
                                                                <li><a href="/user/{{$advisor_row->id}}">
                                                                        {{$advisor_row->name}}</a></li>
                                                            @endforeach
                                                        </ol>
                                                    @else
                                                        <span class="text-yellow-500">Not set yet</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if(Auth::user()->isSuperAdmin() && $user->isAdvisor())
                                            <div class="table-row">
                                                <div class="table-cell pb-2">{{__('Regional Admin')}}</div>
                                                <div class="table-cell pb-2">
                                                    @if($user->regionalAdminByAdvisor)
                                                        <span><a
                                                                href="/user/{{$user->regionalAdminByAdvisor->pluck('id')->first()}}">
                                                                {{$user->regionalAdminByAdvisor->pluck('name')->first()}}</a></span>
                                                    @else
                                                        <span class="text-red-700">Error!</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="table-row">
                                                <div class="table-cell pb-2">{{__('Clients:')}}</div>
                                                <div class="table-cell pb-2">
                                                    @if($user->clientsByAdvisor)
                                                        <ol class="list-disc">
                                                            @foreach ($user->clientsByAdvisor as $client_row)
                                                                <li><a href="/user/{{$client_row->id}}">
                                                                        {{$client_row->name}}</a></li>
                                                            @endforeach
                                                        </ol>
                                                    @else
                                                        <span class="text-yellow-500">Not set yet</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if(Auth::user()->isSuperAdmin() && $user->isClient())
                                            <div class="table-row">
                                                <div class="table-cell pb-2">{{__('Advisor')}}</div>
                                                <div class="table-cell pb-2">
                                                    @if($user->advisorByClient)
                                                        <span><a
                                                                href="/user/{{$user->advisorByClient->pluck('id')->first()}}">
                                                                {{$user->advisorByClient->pluck('name')->first()}}</a></span>
                                                    @else
                                                        <span class="text-red-700">Error!</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isRegionalAdmin() && $user->isAdvisor())

                                            @if ($user->isClient())
                                                <div class="table-row">
                                                    <div class="table-cell pb-2">{{__('Business')}}</div>
                                                    <div class="table-cell pb-2">
                                                        @if(count($user->businesses))
                                                            @foreach ($user->businesses as $business)
                                                                <div>
                                                                    <a href="/business/{{$business->id}}">{{$business->name}}</a>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-red-700">No businesses</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($user->isAdvisor())
                                                <div class="table-row">
                                                    <div class="table-cell pb-2">{{__('Licenses')}}</div>
                                                    <div class="table-cell pb-2">
                                                        @if($user->advisorsLicenses->last()->licenses - count($user->licenses) < 0)
                                                            <x-ui.badge background="bg-red-700">
                                                                {{count($user->licenses) .' '. __('from').' '. $user->advisorsLicenses->last()->licenses}}</x-ui.badge>
                                                        @else
                                                            <x-ui.badge>{{count($user->licenses) .' '. __('from').' '. $user->advisorsLicenses->last()->licenses}}</x-ui.badge>
                                                        @endif

                                                        @if(count($user->licenses))
                                                            @if(Auth::user()->isRegionalAdmin())
                                                                {{count($user->licenses)}}
                                                            @else
                                                                <ol class="list-disc">
                                                                    @foreach ($user->licenses as $business)
                                                                        <li>
                                                                            <a href="/business/{{$business->id}}">{{$business->name}}</a>
                                                                        </li>
                                                                    @endforeach
                                                                </ol>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(count($user->collaborations))
                                                <div class="table-row">
                                                    <div class="table-cell pb-2">{{__('Collaborations')}}</div>
                                                    <div class="table-cell pb-2">
                                                        @if(Auth::user()->isRegionalAdmin())
                                                            {{count($user->collaborations)}}
                                                        @else
                                                            <ol class="list-disc">
                                                                @foreach ($user->collaborations as $business)
                                                                    <li>
                                                                        <a href="/business/{{$business->id}}">{{$business->name}}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ol>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            @endif
                                        @endif
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Email Adress')}}</div>
                                            <div class="table-cell pb-2">{{ $user->email }}</div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Title')}}</div>
                                            <div class="table-cell pb-2">{{ $user->title }}</div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Responsibility')}}</div>
                                            <div class="table-cell pb-2">{{ $user->responsibility }}</div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Last Login')}}</div>
                                            <div class="table-cell pb-2">{{ $user->last_login_at ?: 'Unknown' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>

    </x-ui.main>

</x-app-layout>
