<nav class="relative flex flex-wrap items-center content-between py-3 px-4  text-black bg-white shadow-sm">
    <div class="container mx-auto sm:px-4">
        <a class="inline-block pt-1 pb-1 mr-4 text-lg whitespace-no-wrap" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="py-1 px-2 text-md leading-normal bg-transparent border border-transparent rounded" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="px-5 py-1 border border-gray-600 rounded"></span>
        </button>

        <div class="hidden flex-grow items-center" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="flex flex-wrap list-reset pl-0 mb-0 mr-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="flex flex-wrap list-reset pl-0 mb-0 ml-auto">
                <!-- Authentication Links -->
                @guest
                    <x-nav-link route="login">{{ __('Login') }}</x-nav-link>
                    @if (Route::has('register'))
                    <x-nav-link route="register">{{ __('Register') }}</x-nav-link>
                    @endif
                @else
                    <x-nav-link route="home">{{ __('Home') }}</x-nav-link>
                    <x-nav-link route="businesses">{{ __('Businesses') }}</x-nav-link>
                    {{-- <x-nav-link route="allocations">{{ __('Allocations') }}</x-nav-link> --}}
                    <li class=" relative">

                        <a id="navbarDropdown" class="inline-block py-2 px-4 no-underline  inline-block w-0 h-0 ml-1 align border-b-0 border-t-1 border-r-1 border-l-1" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class=" absolute left-0 z-50 float-left hidden list-reset	 py-2 mt-1 text-base bg-white border border-gray-300 rounded dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <x-dropdown-link route="users" >{{ __('User List') }}</x-nav-link>
                            <x-logout-link>{{ __('Logout') }}</x-nav-link>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
