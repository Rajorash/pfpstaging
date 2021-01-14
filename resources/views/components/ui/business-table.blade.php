<x-ui.table>
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Business Name
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Owner
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Advisor
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Accounts
            </th>
            <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">See Allocations</span>
            </th>
            <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">See Percentages</span>
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">

        @foreach ($businesses as $business)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ $business->name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <img class="h-10 w-10 rounded-full" src="{{ $business->owner->profile_photo_url }}" alt="">
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $business->owner->name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $business->owner->email }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{$business->license->advisor->name}}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <a class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800" href="/business/{{$business->id}}/accounts">{{$business->accounts()->count()}}</a>
            </td>
            <td>
                <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-blue-500 text-white hover:bg-blue-600 py-1 px-2 leading-tight text-xs " href="/allocations/{{$business->id}}">See Allocations</a>
            </td>
            <td>
                <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline bg-blue-500 text-white hover:bg-blue-600 py-1 px-2 leading-tight text-xs " href="/allocations/{{$business->id}}\percentages">See Percentages</a>
            </td>
        </tr>
        @endforeach

    </tbody>
</x-ui.table>
