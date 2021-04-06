<div class="rounded-xl">
    <table id="allocationTable" cellpadding="0" cellspacing="0"
           class="border-collapse rounded-xl bg-white w-full text-dark_gray2">
        <thead>
        <tr>
            <th class="border border-gray-300 rounded-t-xl w-10">
                <span id="processCounter" class="hidden opacity-50 font-normal text-xs"></span>
            </th>
            @foreach($period as $date)
                @php
                    $date = Carbon\Carbon::parse($date);
                @endphp
                <th class="w-24 border border-gray-300 p-4 {{ $date->isToday() ? 'text-blue': '' }} ">
                    <span class="block text-xs font-normal">{{$date->format('M Y')}}</span>
                    <span class="block text-xl">{{$date->format('j')}}</span>
                    <span class="block text-xs font-normal">{{$date->format('D')}}</span>
                </th>
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
                        <td class="border border-gray-300 whitespace-nowrap bg-indigo-100">{{$data['name']}}</td>
                        @foreach($period as $date)
                            <td class="border border-gray-300 text-right bg-indigo-100"><input
                                    class="px-2 py-1 text-right bg-transparent border-none w-full"
                                    id="account_{{$id}}_{{$date->format('Y-m-d')}}"
                                    type="text" value="{{$data[$date->format('Y-m-d')]}}" disabled/></td>
                        @endforeach
                    </tr>
                    @foreach($data as $key => $ext_data)
                        @if(is_array($ext_data))
                            <tr>
                                <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">{{$ext_data['name']}}</td>
                                @foreach($period as $date)
                                    <td class="border border-gray-300 text-right p-0 hover:bg-yellow-100"><input
                                            class="px-2 py-1 w-full text-right bg-transparent border-0
                                            border-transparent outline-none
                                            focus:outline-none focus:ring-1 focus:shadow-none focus:bg-white"
                                            id="flow_{{$key}}_{{$date->format('Y-m-d')}}"
                                            type="text" value="{{$ext_data[$date->format('Y-m-d')]}}"/></td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            @else
                @foreach($accounts as $id => $data)
                    <tr>
                        <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-4 bg-indigo-100">{{$data['name']}}</td>
                        @foreach($period as $date)
                            <td class="border border-gray-300 text-right p-0 bg-indigo-100"><input
                                    class="px-2 py-1 w-full text-right bg-transparent border-none"
                                    type="text" value="{{$data[$date->format('Y-m-d')]}}" disabled/></td>
                        @endforeach
                    </tr>
                    @foreach($data as $key => $ext_data)
                        @if(is_array($ext_data))
                            @if($key == 'transfer')
                                <tr>
                                    <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">
                                        Transfer In
                                    </td>
                                    @foreach($period as $date)
                                        <td class="border border-gray-300 text-right p-0"><input
                                                class="px-2 py-1 w-full text-right bg-transparent border-none"
                                                type="text" value="{{$ext_data[$date->format('Y-m-d')]}}" disabled/>
                                        </td>
                                    @endforeach
                                </tr>
                            @elseif($key == 'total')
                                <tr>
                                    <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">
                                        Flow Total
                                    </td>
                                    @foreach($period as $date)
                                        <td class="border border-gray-300 text-right p-0"><input
                                                class="px-2 py-1 w-full text-right bg-transparent border-none"
                                                type="text" value="{{$ext_data[$date->format('Y-m-d')]}}" disabled/>
                                        </td>
                                    @endforeach
                                </tr>
                            @elseif(is_integer($key))
                                <tr>
                                    <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-4 bg-indigo-100">{{$ext_data['name']}}</td>
                                    @foreach($period as $date)
                                        <td class="border border-gray-300 text-right p-0 bg-indigo-100 hover:bg-yellow-100">
                                            <input
                                                class="px-2 py-1 w-full text-right bg-transparent border-0
                                                border-transparent outline-none
                                                focus:outline-none focus:ring-1 focus:shadow-none focus:bg-white"
                                                id="flow_{{$key}}_{{$date->format('Y-m-d')}}"
                                                type="text" value="{{$ext_data[$date->format('Y-m-d')]}}"/></td>
                                    @endforeach
                                </tr>
                            @endif
                        @endif
                    @endforeach
                    <tr class="bg-light_blue">
                        <td class="h-1" colspan="{{$range+1}}"></td>
                    </tr>
                @endforeach
            @endif

        @endforeach

        </tbody>
    </table>
</div>
