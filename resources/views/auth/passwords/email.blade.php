<x-guest-layout>

    <div class="container pt-48 mx-auto sm:px-4">
        <div class="flex flex-wrap content-center justify-center">
            <div class="pl-4 pr-4 my-auto md:w-2/3">
                <div
                class="relative flex flex-col min-w-0 break-words bg-white border border-gray-300 rounded border-1">
                <div
                class="px-6 py-3 mb-0 text-gray-900 bg-gray-200 border-gray-300 border-b-1">{{ __('Reset Password') }}</div>

                <div class="flex-auto p-6">
                    @if (session('status'))
                    <div
                    class="relative px-3 py-3 mb-4 text-green-800 bg-green-200 border border-green-300 rounded"
                    role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="flex flex-wrap mb-4 ">
                        <label for="email"
                        class="pt-2 pb-2 pl-4 pr-4 mb-0 leading-normal md:w-1/3 md:text-right">{{ __('E-Mail Address') }}</label>

                        <div class="pl-4 pr-4 md:w-1/2">
                            <input id="email" type="email"
                            class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded @error('email') bg-red-700 @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email"
                            autofocus>

                            @error('email')
                            <span class="hidden mt-1 text-sm text-red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-wrap mb-4">
                        <div class="pl-4 ml-auto md:w-1/2">
                            <button type="submit"
                            class="inline-block px-3 py-1 ml-auto font-normal leading-normal text-center text-white no-underline whitespace-no-wrap align-middle border rounded select-none bg-blue hover:bg-dark_gray2">
                            {{ __('Send Password Reset Link') }}
                           </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


</x-guest-layout>
