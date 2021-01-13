<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Businesses > Details
        </h2>
    </x-slot>

    <x-ui.card>

        <x-slot name="header">
            <h2 class="text-lg leading-6 font-medium text-black">This business is:</h2>
        </x-slot>

        <a class="text-blue-500 hover:text-blue-700" href="/business/{{$business->id}}"><strong>{{ $business->name }}</strong></a><br>
        Owner: <a class="text-blue-500 hover:text-blue-700" href="/user/{{$business->owner->id}}">{{$business->owner->name}}</a><br>
        Advisor: {{$business->license ? $business->license->advisor->name : 'No advisor.'}}

    </x-ui.card>

</x-app-layout>
