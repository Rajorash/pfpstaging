<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo/>
        </x-slot>

        <x-jet-validation-errors class="mb-4"/>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @if (Config::get('app.env') == 'local')
                <div class="flex pb-2">
                    <a class="px-2 py-1 mr-2 border rounded border-gray-400 hover:bg-gray-100 cursor-pointer text-sm"
                       onclick="(function(){$('#email').val('superadmin@pfp.com');$('#password').val('#j$dZW|bdYO+`CW`~,y|');})();return false;">
                        Super Admin</a>
                    <a class="px-2 py-1 mr-2 border rounded border-gray-400 hover:bg-gray-100 cursor-pointer text-sm"
                       onclick="(function(){$('#email').val('regionaladmin@pfp.com');$('#password').val('#j$dSYUD(W@SbdYO+`CW');})();return false;">
                        Regional Admin</a>
                    <a class="px-2 py-1 mr-2 border rounded border-gray-400 hover:bg-gray-100 cursor-pointer text-sm"
                       onclick="(function(){$('#email').val('advisor@pfp.com');$('#password').val('letmeinnow!');})();return false;">
                        Test Advisor</a>
                    <a class="px-2 py-1 mr-2 border rounded border-gray-400 hover:bg-gray-100 cursor-pointer text-sm"
                       onclick="(function(){$('#email').val('craig@mintscdconsulting.com.au');$('#password').val('CML9Zy!&$H2#e@e9');})();return false;">
                        Craig Minter</a>
                    <a class="px-2 py-1 mr-2 border rounded border-gray-400 hover:bg-gray-100 cursor-pointer text-sm"
                       onclick="(function(){$('#email').val('client@pfp.com');$('#password').val('letmeinnow!');})();return false;">
                        Test Client</a>
                </div>
            @endif

            <div>
                <x-jet-label for="email" value="{{ __('Email') }}"/>
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                             required autofocus/>
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}"/>
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required
                             autocomplete="current-password"/>
            </div>

            <div class="block mt-4">
                <div class="grid grid-cols-2">
                    <div>
                        <label for="remember_me" class="flex items-center">
                            <x-jet-checkbox id="remember_me" name="remember"/>
                            <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                    </div>
                    <div class="text-right">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-blue hover:text-gray-900"
                               href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-8">
                <x-jet-button class="w-full text-center py-4 uppercase font-normal bg-blue text-base">
                    {{ __('Login now') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
