@extends('layouts.app')

@section('content')
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-2/3 pr-4 pl-4">
            <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900"><strong>Update Flow For {{$account->name}}</strong></div>

                <div class="flex-auto p-6">
                    <form method="POST" action="/accounts/{{$account->id}}/flow/{{$flow->id}}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4 flex flex-wrap ">
                            <label for="label" class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal md:text-right">{{ __('Label') }}</label>

                            <div class="md:w-1/2 pr-4 pl-4">
                                <input id="label" type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded @error('label') bg-red-700 @enderror" name="label" value="{{ $flow->label }}" required autocomplete="label" autofocus>

                                @error('label')
                                    <span class="hidden mt-1 text-sm text-red" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 flex flex-wrap ">
                            <label class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal md:text-right">Flow Type:</label>
                            <div class="md:w-1/2 pr-4 pl-4">
                                <div class="relative inline-flex align-middle" data-toggle="buttons">
                                    <label class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline bg-green-500 text-white hover:green-600 active">
                                        <input type="radio" name="flow-direction" id="flow-in" autocomplete="off" value="0" {{ $flow->isNegative() ? '' : 'checked' }}>
                                        Positive
                                    </label>
                                    <label class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline bg-red-600 text-white hover:bg-red-700">
                                        <input type="radio" name="flow-direction" id="flow-out" autocomplete="off" value="1" {{ $flow->isNegative() ? 'checked' : '' }}>
                                        Negative
                                    </label>
                                </div>
                            </div>
                                
                            @error('flow-direction')
                            <span class="hidden mt-1 text-sm text-red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-4 flex flex-wrap  mb-0">
                            <div class="md:w-1/2 pr-4 pl-4 md:mx-1/3">
                                <button type="submit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline bg-blue-600 text-white hover:bg-blue-600">
                                    {{ __('Update Flow') }}
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
