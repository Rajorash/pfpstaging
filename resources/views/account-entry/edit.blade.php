@extends('layouts.app')

@section('content')
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-2/3 pr-4 pl-4">
            @if (\Session::has('success'))
                <div class="relative px-3 py-3 mb-4 border rounded bg-green-200 border-green-300 text-green-800  opacity-0 opacity-100 block">
                    <h4 class="">Success!</h4>
                    <p>{!! \Session::get('success') !!}</p>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900"><strong>Enter Account Balances for {{$business->name}}</strong></div>

                <div class="flex-auto p-6">

                    <x-forms.account-entry :business="$business" />

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
