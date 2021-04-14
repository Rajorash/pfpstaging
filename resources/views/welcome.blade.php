<x-guest-layout>
    <div class="h-screen w-screen">
        <div class="pt-48">
            <h1 class="text-blue text-center text-5xl">PFP MVP</h1>
            <h2 class="pt-8 text-dark_gray2 text-center text-xl">Coming soon</h2>
        </div>

        @if (Route::has('login'))
            <div>
                <div class="text-center w-48 space-x-6 pt-24 px-6 mx-auto">
                    @auth
                        <x-ui.button-normal href="{{ url('/dashboard') }}">Dashboard</x-ui.button-normal>
                    @else
                        <x-ui.button-normal href="{{ route('login') }}">Login</x-ui.button-normal>

                        @if (Route::has('register'))
                            <x-ui.button-normal href="{{ route('register') }}">Register</x-ui.button-normal>
                        @endif
                    @endauth
                </div>
            </div>
        @endif

    </div>
</x-guest-layout>
