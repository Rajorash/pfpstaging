<x-guest-layout>
    <div class="flex flex-col h-screen w-screen">
    @if (Route::has('login'))
    <div>
        <div class="flex flex-row content-end w-full space-x-6 py-3 px-6">
            @auth
            <a class="text-blue-400 hover:text-blue-600 block ml-auto" href="{{ url('/dashboard') }}">Dashboard</a>
            @else
            <a class="text-blue-400 hover:text-blue-600 block ml-auto" href="{{ route('login') }}">Login</a>

            @if (Route::has('register'))
            <a class="text-blue-400 hover:text-blue-600 block" href="{{ route('register') }}">Register</a>
            @endif
            @endauth
        </div>
    </div>
    @endif

    <div class="flex-grow">
        <h1 class="font-sans text-gray-600 subpixel-antialiased text-center text-5xl">PFP MVP</h1>
        <h2 class="font-sans text-gray-600 subpixel-antialiased text-center text-xl">Coming soon</h2>
    </div>
    </div>
</x-guest-layout>
