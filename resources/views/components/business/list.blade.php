<div>


    <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900"><strong>Select A Business To See It's Allocations</strong></div>

    <div class="flex-auto p-6 p-0">
        <table class="w-full max-w-full mb-4 bg-transparent table-striped">
            <thead class="thead-inverse">
                <tr>
            <th>Business Name</th>
            <th>Owner</th>
            <th>Advisor</th>
            <th class="text-center">Accounts</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            @forelse ($businesses as $business)
        <tr>
            <td class="py-2" scope="row">
                <a href="/business/{{$business->id}}"><strong>{{ $business->name }}</strong></a>
            </td>
            <td class="py-2">
                <a href="/user/{{$business->owner->id}}">{{$business->owner->name}}</a>
            </td>
            <td class="py-2">
                {{$business->license ? $business->license->advisor->name : 'No advisor.'}}
            </td>
            <td class="text-center">
                <a href="/business/{{$business->id}}/accounts">{{$business->accounts()->count()}}</a>
            </td>
        </td>
            {{-- <td class="text-center">
                <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-orange-400 text-black hover:bg-orange-500 py-1 px-2 leading-tight text-xs " href="/business/{{$business->id}}/tax">Tax rate</a>
            </td> --}}
            <td>
                <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-blue text-white hover:bg-blue-600 py-1 px-2 leading-tight text-xs " href="/allocations/{{$business->id}}">See Allocations</a>
            </td>
            <td>
                <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-blue text-white hover:bg-blue-600 py-1 px-2 leading-tight text-xs " href="/allocations/{{$business->id}}\percentages">See Percentages</a>
            </td>
        </tr>
        @empty
        <tr>
            <td scope="row">N/A</td>
            <td>N/A</td>
            <td>N/A</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

</div>
