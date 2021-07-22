<x-app-layout>
    <x-slot name="header">
        {{ __('Users') }}
    </x-slot>

    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption>
                <span>Users Visible To You</span>
                @if(
                    Auth::user()->isSuperAdmin() ||
                    Auth::user()->roles->pluck('name')->contains('admin') ||
                    Auth::user()->roles->pluck('name')->contains('advisor')
                    )
                    <x-slot name="right">
                        <x-ui.button-normal href="{{route('users.create')}}">
                            <x-icons.user-add/>
                            <span class="ml-2">Create User</span>
                        </x-ui.button-normal>
                    </x-slot>
                @endif
            </x-ui.table-caption>
            <thead>
            <tr class="border-light_blue border-t border-b">
                <x-ui.table-th padding="pl-12 pr-2 py-4">Name</x-ui.table-th>
                <x-ui.table-th>Title</x-ui.table-th>
                <x-ui.table-th class="text-center">Status</x-ui.table-th>
                @if(Auth::user()->isSuperAdmin() || Auth::user()->isRegionalAdmin())
                    <x-ui.table-th>Available seats</x-ui.table-th>
                @endif
                <x-ui.table-th>Roles</x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th class="mr-12"></x-ui.table-th>
            </tr>
            </thead>

            <x-ui.table-tbody>
                @if (count($users))
                    @foreach ($users as $user)
                        <tr>
                            <x-ui.table-td class="whitespace-nowrap"
                                           padding="pl-12 pr-2 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_url }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $user->email }}
                                        </div>
                                        @if(Auth::user()->isSuperAdmin())
                                            <div class="ml-4">
                                                @if($user->isRegionalAdmin())
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        Advisors: {{count($user->advisorsByRegionalAdmin)}}
                                                    </div>
                                                @endif

                                                @if($user->isAdvisor())
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        Regional Admin:
                                                        @if($user->regionalAdminByAdvisor)
                                                            <span><a
                                                                    href="/user/{{$user->regionalAdminByAdvisor->pluck('id')->first()}}">
                                                                    {{$user->regionalAdminByAdvisor->pluck('name')->first()}}</a></span>
                                                        @else
                                                            <span class="text-red-700">Error!</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        Clients: {{count($user->clientsByAdvisor)}}
                                                    </div>
                                                @endif

                                                @if($user->isClient())
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        Advisor:
                                                        @if($user->advisorByClient)
                                                            <span><a
                                                                    href="/user/{{$user->advisorByClient->pluck('id')->first()}}">
                                                                    {{$user->advisorByClient->pluck('name')->first()}}</a></span>
                                                        @else
                                                            <span class="text-red-700">Error!</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isRegionalAdmin())
                                            <div class="ml-4">
                                                @if($user->isAdvisor())
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        Licensed businesses: {{count($user->licenses)}} <br>
                                                        Collaborated businesses: {{count($user->collaborations)}}
                                                    </div>
                                                @endif
                                                @if($user->isClient())
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        Businesses as Client: {{count($user->businesses)}}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </x-ui.table-td>
                            <x-ui.table-td>
                                <div class="text-sm text-dark_gray2">{{ $user->title }}</div>
                                <div class="text-sm text-light_gray">{{ $user->responsibility }}</div>
                            </x-ui.table-td>
                            <x-ui.table-td class="text-center">
                                @if($user->isActive())
                                    <x-ui.badge>Active</x-ui.badge>
                                @else
                                    <x-ui.badge background="bg-light_gray">Inactive</x-ui.badge>
                                @endif
                            </x-ui.table-td>

                            @if(Auth::user()->isSuperAdmin() || Auth::user()->isRegionalAdmin())
                                <x-ui.table-td class="text-center">
                                    @if ($user->isAdvisor())
<<<<<<< HEAD
                                        @if ($user->seats)
                                            @if($user->seats - count($user->activeLicenses) < 0)
=======
                                        @if ($user->advisorsLicenses->last())
                                            @php
                                                $availableSeats = $user->seats - count($user->licenses);
                                            @endphp

                                            @if($availableSeats < 0)
>>>>>>> mykhailo
                                                <x-ui.badge background="bg-red-700">
                                                    {{$availableSeats .' / '. $user->seats}}</x-ui.badge>
                                            @else
<<<<<<< HEAD
                                                <x-ui.badge>{{count($user->activeLicenses) .' '. __('from').' '. $user->seats}}</x-ui.badge>
=======
                                                <x-ui.badge>{{$availableSeats .' / '. $user->seats}}</x-ui.badge>
>>>>>>> mykhailo
                                            @endif

                                            @if (count($user->notActiveLicenses))
                                                <br/>
                                                <x-ui.badge background="bg-gray-500">
                                                    Disabled: {{count($user->notActiveLicenses)}}
                                                </x-ui.badge>
                                            @endif

                                        @else
                                            <x-ui.badge background="bg-red-700">{{__('Not set')}}</x-ui.badge>
                                        @endif

                                    @endif
                                </x-ui.table-td>
                            @endif

                            <x-ui.table-td>
                                @if (!empty($user->roles->pluck('label')->toArray()))
                                    {{ implode(', ',$user->roles->pluck('label')->toArray()) }}
                                @else
                                    <span class="text-red-700">Error! User hasn't any role</span>
                                @endif
                            </x-ui.table-td>
                            <x-ui.table-td>
                                <x-ui.button-small href="{{url('/user/'.$user->id)}}">
                                    See User
                                </x-ui.button-small>
                            </x-ui.table-td>
                            <x-ui.table-td>
                                @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdvisor())
                                    <x-ui.button-small href="{{route('users.edit', ['user'=>$user])}}">
                                        Edit User
                                    </x-ui.button-small>
                                @endif
                            </x-ui.table-td>
                            <x-ui.table-td>
                                @if(Auth::user()->isRegionalAdmin() && $user->isAdvisor())
                                    <x-ui.button-small href="{{route('licenses.list', ['user'=>$user])}}">
                                        Licenses
                                    </x-ui.button-small>
                                    {{--                                    <livewire:licenses-counter :user="$user" :key="$user->id"/>--}}
                                @endif
                            </x-ui.table-td>
                        </tr>
                    @endforeach
                @endif
            </x-ui.table-tbody>

        </x-ui.table-table>
        <div class="m-6">
            @if ($users)
                {{ $users->links() }}
            @endif
        </div>
    </x-ui.main>

</x-app-layout>
