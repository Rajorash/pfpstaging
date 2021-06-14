@extends('layouts.app')

@section('content')
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-2/3 pr-4 pl-4">
            <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900">Tax Rates</div>

                <div class="flex-auto p-6">
                    @forelse ($salestaxAccounts as $acc)
                    <form action="/taxrate" class="flex items-center" method="POST">
                        @csrf
                        {!! Form::hidden('account_id', $acc->id) !!}
                        <label for="taxrate" class="ml-auto mr-2 text-right">
                            {{$acc->name}}
                        </label>
                        <div class="relative flex items-stretch w-full mb-2 sm:mr-2">
                        <input type="text" name="rate" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded text-right" id="taxrate" placeholder="10" value="{{$acc->taxRate->rate ?? ''}}">
                            <div class="input-group-append">
                                <div class="input-group-text">%</div>
                            </div>
                          </div>
                          <button type="submit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2 mb-2">Submit</button>
                    </form>
                    @empty
                    No accounts with 'salestax' designation.
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
