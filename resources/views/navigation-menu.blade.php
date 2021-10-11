<nav x-data="{ open: false }" class="bg-white border-b border-light_blue">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-24">
            <div class="flex w-full">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-jet-application-mark class="block w-auto h-9"/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-20 sm:flex">
                    <x-jet-nav-link href="{{ route('dashboard') }}"
                                    :active="request()->is('*dashboard.*') || request()->is('*dashboard*')">
                        {{ __('Dashboard') }}
                    </x-jet-nav-link>
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdvisor() || Auth::user()->isClient())
                        <x-jet-nav-link href="{{ route('allocation-calculator') }}"
                                        :active="request()->routeIs('allocation-calculator')">
                            {{ __('Calculator') }}
                        </x-jet-nav-link>
                        <x-jet-nav-link href="{{ route('businesses') }}"
                                        :active="request()->is('*business/*') || request()->is('business')">
                            {{ __('Businesses') }}
                        </x-jet-nav-link>
                    @endif
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isRegionalAdmin() || Auth::user()->isAdvisor())
                        <x-jet-nav-link href="{{ route('users') }}" :active="request()->routeIs('users')">
                            @if (Auth::user()->isRegionalAdmin()
                                    && !Auth::user()->isSuperAdmin()
                                    && !Auth::user()->isAdvisor()
                                    && !Auth::user()->isClient())
                                {{ __('Advisors') }}
                            @else
                                {{ __('Users') }}
                            @endif
                        </x-jet-nav-link>
                    @endif
                </div>
                <div class="relative flex items-center flex-shrink-0 ml-auto">
                    <div class="text-xs">
                        <div class="-mt-4 text-right opacity-50">{{Auth::user()->timezone}}</div>
                        Your local time: <span id="your_time"
                                                          data-time="{{ Timezone::convertToLocal(\Carbon\Carbon::now(), 'Y-m-d')
                                                                    .'T'.Timezone::convertToLocal(\Carbon\Carbon::now(), 'H:i:s')}}">
                        {{ Timezone::convertToLocal(\Carbon\Carbon::now(), 'H:i') }}
                        </span>
                    </div>
                    <script type="text/javascript">
                        let startTime = new Date(document.getElementById('your_time').dataset.time);
                        setInterval(function () {
                            startTime = new Date(startTime.getTime() + 1000);
                            document.getElementById('your_time').innerText =
                                (startTime.getHours() < 10 ? '0' + startTime.getHours() : startTime.getHours())
                                + ':' + (startTime.getMinutes() < 10 ? '0' + startTime.getMinutes() : startTime.getMinutes());
                        }, 1000);
                    </script>
                    <div class="absolute right-0 text-xs bottom-2"><a class="opacity-30 hover:opacity-100 hover:underline" href="{{ route('profile.show') }}">Change timezone</a></div>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative ml-3">
                        <x-jet-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ml-2 -mr-0.5 mb-2 font-medium leading-tight text-xl w-4"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-jet-dropdown-link
                                        href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-jet-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-jet-dropdown-link>
                                    @endcan

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Team Switcher -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Switch Teams') }}
                                    </div>

                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-jet-switchable-team :team="$team"/>
                                    @endforeach
                                </div>
                            </x-slot>
                        </x-jet-dropdown>
                    </div>
            @endif

            <!-- Settings Dropdown -->
                <div class="relative ml-3">
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button title="{{ Auth::user()->name }}"
                                        class="flex text-sm transition duration-150 ease-in-out border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                                    <img class="object-cover w-8 h-8 rounded-full"
                                         src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}"
                                         title="{{ Auth::user()->name }}"/>
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                                        {{ Auth::user()->name }}

                                        <svg class="ml-2 -mr-0.5 mb-2 font-medium leading-tight text-xl w-4"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                <span class="inline-block mr-4 pt-0.5 w-4 text-center"><x-icons.profile
                                        :class="'w-4 h-auto'"/></span>
                                {{ __('Profile') }}
                            </x-jet-dropdown-link>

                            @if(Auth::user()->isSuperAdmin()
                                || Auth::user()->isAdvisor()
                                || Auth::user()->isClient())
                                <x-jet-dropdown-link href="{{ route('businesses') }}">
                                <span class="inline-block mr-4 pt-0.5 w-4 text-center"><x-icons.case
                                        :class="'w-4 h-auto'"/></span>
                                    {{ __('Create A Business') }}
                                </x-jet-dropdown-link>
                            @endif

                            @if(
                                Auth::user()->isSuperAdmin()
                                || Auth::user()->isRegionalAdmin()
                                || Auth::user()->isAdvisor()
                                )
                                <x-jet-dropdown-link href="{{route('users.create')}}">
                                <span class="inline-block mr-4 pt-0.5 w-4 text-center"><x-icons.user-add
                                        :class="'w-4 h-auto'"/></span>
                                    {{ __('Add A User') }}
                                </x-jet-dropdown-link>
                            @endif

                            {{--                            <x-jet-dropdown-link href="#">--}}
                            {{--                                <span class="inline-block mr-4 pt-0.5 w-4 text-center"><x-icons.gear--}}
                            {{--                                        :class="'w-4 h-auto'"/></span>--}}
                            {{--                                {{ __('Settings') }}--}}
                            {{--                            </x-jet-dropdown-link>--}}

                            @if (Auth::user()->isSuperAdmin())
                                <x-jet-dropdown-link href="{{route('maintenance')}}">
                                <span class="inline-block mr-4 pt-0.5 w-4 text-center"><x-icons.maintenance
                                        :class="'w-4 h-auto'"/></span>
                                    {{ __('Maintenance') }}
                                </x-jet-dropdown-link>
                            @endif

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-jet-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}"
                                                     onclick="event.preventDefault(); this.closest('form').submit();">
                                    <span class="inline-block mr-4 pt-0.5 w-4 text-center"><x-icons.logout
                                            :class="'w-4 h-auto'"/></span>
                                    {{ __('Logout') }}
                                </x-jet-dropdown-link>
                            </form>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -mr-2 sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <svg class="w-6 mb-2 text-base font-medium leading-tight" stroke="currentColor" fill="none"
                         viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-jet-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-jet-responsive-nav-link>
            @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdvisor() || Auth::user()->isClient())
                <x-jet-responsive-nav-link href="{{ route('allocation-calculator') }}"
                                           :active="request()->routeIs('allocation-calculator')">
                    {{ __('Calculator') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('businesses') }}"
                                           :active="request()->is('*business/*') || request()->is('business')">
                    {{ __('Businesses') }}
                </x-jet-responsive-nav-link>
            @endif
            @if(Auth::user()->isSuperAdmin() || Auth::user()->isRegionalAdmin() || Auth::user()->isAdvisor())
                <x-jet-responsive-nav-link href="{{ route('users') }}" :active="request()->routeIs('users')">
                    @if (Auth::user()->isRegionalAdmin()
                            && !Auth::user()->isSuperAdmin()
                            && !Auth::user()->isAdvisor()
                            && !Auth::user()->isClient())
                        {{ __('Advisors') }}
                    @else
                        {{ __('Users') }}
                    @endif
                </x-jet-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="flex-shrink-0 mr-3">
                        <img class="object-cover w-10 h-10 rounded-full" src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ Auth::user()->name }}"/>
                    </div>
                @endif

                <div>
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-jet-responsive-nav-link href="{{ route('profile.show') }}"
                                           :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-jet-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-jet-responsive-nav-link href="{{ route('api-tokens.index') }}"
                                               :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-jet-responsive-nav-link>
                @endif

            <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-jet-responsive-nav-link href="{{ route('logout') }}"
                                               onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        {{ __('Logout') }}
                    </x-jet-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-jet-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                                               :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-jet-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-jet-responsive-nav-link href="{{ route('teams.create') }}"
                                                   :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-jet-responsive-nav-link>
                    @endcan

                    <div class="border-t border-gray-200"></div>

                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Switch Teams') }}
                    </div>

                    @foreach (Auth::user()->allTeams() as $team)
                        <x-jet-switchable-team :team="$team" component="jet-responsive-nav-link"/>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</nav>
