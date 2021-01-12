@extends('layouts.app')

@section('content')
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-2/3 pr-4 pl-4">
            <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900"><strong>Create A New Account For {{$business->name}}</strong></div>

                <div class="flex-auto p-6">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
