<x-guest-layout>
    <div class="w-screen h-screen">
        <div class="pt-48">
            <h1 class="text-5xl text-center text-blue">{{__('PFP MVP')}}</h1>
            <h2 class="pt-8 text-xl text-center text-dark_gray2">{{__('Coming soon')}}</h2>
        </div>

        @if (Route::has('login'))
            <div>
                <div class="w-48 px-6 pt-24 mx-auto space-x-6 text-center">
                    @auth
                        <x-ui.button-normal href="{{ url('/dashboard') }}">{{__('Dashboard')}}</x-ui.button-normal>
                    @else
                        <x-ui.button-normal href="{{ route('login') }}">{{__('Login')}}</x-ui.button-normal>

                        @if (Route::has('register'))
                            <x-ui.button-normal href="{{ route('register') }}">{{__('Register')}}</x-ui.button-normal>
                        @endif
                    @endauth
                </div>
            </div>
        @endif

    </div>
</x-guest-layout>
