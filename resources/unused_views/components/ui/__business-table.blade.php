<x-ui.table>
    <thead class="bg-gray-100">
    <tr class="text-xs uppercase text-dark_gray2">
        <th scope="col" class="px-6 pt-4 pb-3 font-normal text-left">
            Business Name
        </th>
        <th scope="col" class="px-6 pt-4 pb-3 font-normal text-left">
            Owner
        </th>
        <th scope="col" class="px-6 pt-4 pb-3 font-normal text-left">
            Advisor
        </th>
        <th scope="col" class="px-6 pt-4 pb-3 font-normal text-center">
            Accounts
        </th>
        <th scope="col" class="relative px-6 pt-4 pb-3 font-normal">
            <span class="sr-only">See Allocations</span>
        </th>
        <th scope="col" class="relative px-6 pt-4 pb-3 font-normal">
            <span class="sr-only">See Percentages</span>
        </th>
    </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">

    @foreach ($businesses as $business)
        <tr class="text-dark_gray2">
            <td class="px-6 py-4 whitespace-nowrap">
                <div>{{ $business->name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10">
                        <img class="w-10 h-10 rounded-full" src="{{ $business->owner->profile_photo_url }}" alt="">
                    </div>
                    <div class="ml-4">
                        <div class="">
                            {{ $business->owner->name }}
                        </div>
                        <div class="text-sm text-light_gray">
                            {{ $business->owner->email }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="">{{$business->license->advisor->name}}</div>
            </td>
            <td class="px-6 py-4 text-center whitespace-nowrap">
                <a class="inline-flex text-xs rounded-full py-0.5 px-2 leading-tight bg-green text-white"
                   href="/business/{{$business->id}}/accounts">{{$business->accounts()->count()}}</a>
            </td>
            <td>
                <a class="inline-block px-3 py-1 text-xs font-normal leading-tight text-center text-white no-underline whitespace-no-wrap align-middle border rounded-lg select-none bg-blue hover:bg-dark_gray2"
                   href="{{route('allocations-new', ['business' => $business])}}">See Allocations</a>
            </td>
            <td>
                <a class="inline-block px-3 py-1 text-xs font-normal leading-tight text-center text-white no-underline whitespace-no-wrap align-middle border rounded-lg select-none bg-blue hover:bg-dark_gray2"
                   href="/allocations/{{$business->id}}\percentages">See Percentages</a>
            </td>
        </tr>
    @endforeach

    </tbody>
</x-ui.table>
