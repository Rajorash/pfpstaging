<table class="w-full max-w-full mb-4 bg-transparent ">
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
        <tr class="border-t border-gray-200">
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
                <a class="text-blue-400 hover:text-blue-600" href="/business/{{$business->id}}/accounts">{{$business->accounts()->count()}}</a>
            </td>
        </td>
        {{-- <td class="text-center">
            <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-orange-400 text-black hover:bg-orange-500 py-1 px-2 leading-tight text-xs " href="/business/{{$business->id}}/tax">Tax rate</a>
        </td> --}}
        <td>
            <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-blue-500 text-white hover:bg-blue-600 py-1 px-2 leading-tight text-xs " href="/allocations/{{$business->id}}">See Allocations</a>
        </td>
        <td>
            <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-blue-500 text-white hover:bg-blue-600 py-1 px-2 leading-tight text-xs " href="/allocations/{{$business->id}}\percentages">See Percentages</a>
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


