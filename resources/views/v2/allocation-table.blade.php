<div class="rounded-xl">
    <table id="allocationTable" cellpadding="0" cellspacing="0" class="border-collapse rounded-xl bg-white w-full">
        <thead>
        <tr>
            <th class="border border-gray-300">
                <span id="processCounter" class="hidden opacity-50 font-normal text-xs"></span>
            </th>
            {{--            today --}}
            <th class="border border-gray-300 text-blue p-4">
                <span class="block text-xs font-normal">{{$startDate->format('M Y')}}</span>
                <span class="block text-xl">{{$startDate->format('j')}}</span>
                <span class="block text-xs font-normal">{{$startDate->format('D')}}</span>
            </th>
            {{--            regular days--}}
            @php
            $first = true;
            @endphp
            @foreach($period as $date)
                @if($first)
                    @php($first = false)
                @else
                <th class="border border-gray-300 p-4">
                    <span class="block text-xs font-normal">{{$date->format('M Y')}}</span>
                    <span class="block text-xl">{{$date->format('j')}}</span>
                    <span class="block text-xs font-normal">{{$date->format('D')}}</span>
                </th>
                @endif
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($tableData as $type => $accounts)
            <tr class="bg-blue text-white uppercase">
                <td class="py-1 pr-2 pl-4" colspan="{{$range+1}}">{{ucfirst($type)}} Accounts</td>
            </tr>
            @if($type == 'revenue')
                @foreach($accounts as $id => $data)
                <tr>
                    <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-4 bg-indigo-100">{{$data['name']}}</td>
                    @foreach($period as $date)
                        <td class="border border-gray-300 text-right p-1 bg-indigo-100"><input
                                class="px-2 py-0 w-20 text-right bg-transparent border-none"
                                type="text" value="{{$data[$date->format('Y-m-d')]}}" disabled/></td>
                    @endforeach
                </tr>
                    @foreach($data as $key => $ext_data)
                        @if(is_array($ext_data))
                <tr>
                    <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">{{$ext_data['name']}}</td>
                    @foreach($period as $date)
                    <td class="border border-gray-300 text-right p-1"><input
                            class="px-2 py-0 w-20 text-right bg-transparent border-0 border-b border-transparent outline-none
                    focus:border-yellow-700 focus:outline-none focus:shadow-none focus:ring-0"
                            id="someOwnKey_1_{{$key}}"
                            type="text" value="{{$ext_data[$date->format('Y-m-d')]}}"/></td>
                    @endforeach
                </tr>
                        @endif
                    @endforeach
                @endforeach
            @else

            @endif

        @endforeach

        </tbody>
    </table>
</div>
