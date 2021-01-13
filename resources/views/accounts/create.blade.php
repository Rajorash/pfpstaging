<x-app-layout>
    <x-slot name="header">
        <div class="flex content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$business->name}} > Accounts
            </h2>
            <x-business-nav businessId="{{$business->id}}" />
            </div>

        </x-slot>

        <x-ui.card>

            <x-slot name="header">
                <h2 class="text-lg leading-6 font-medium text-black">Create A New Account For {{$business->name}}</h2>
            </x-slot>

            <form method="POST" action="/business/{{$business->id}}/accounts">
                    @csrf

                    <div class="mb-4 flex flex-wrap ">
                        <label for="name" class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal md:text-right">{{ __('Name') }}</label>

                        <div class="md:w-1/2 pr-4 pl-4">
                            <input id="name" type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded @error('name') bg-red-700 @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                            @error('name')
                            <span class="hidden mt-1 text-sm text-red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap ">
                        <label class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal md:text-right">Account Type:</label>
                        <div class="md:w-1/2 pr-4 pl-4">
                            <select name="account_type" id="account_type" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded ">
                                <option>Select your account type</option>
                                @foreach (App\Models\BankAccount::type_list() as $account_index => $account_type)
                                <option value="{{ $account_index }}"{{ $account_type == old('account_type') ? ' selected' : '' }}>{{ $account_type }}</option>
                                @endforeach
                            </select>
                        </div>

                        @error('account_type')
                        <span class="hidden mt-1 text-sm text-red" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mb-4 flex flex-wrap  mb-0">
                        <div class="md:w-1/2 pr-4 pl-4 md:mx-1/3">
                            <button type="submit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline bg-blue-600 text-white hover:bg-blue-600">
                                {{ __('Create Account') }}
                            </button>
                        </div>
                    </div>
                </form>
        </x-ui.card>

</x-app-layout>
