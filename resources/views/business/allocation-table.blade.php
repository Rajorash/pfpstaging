<x-ui.table-table class="table-sticky-header table-sticky-first-column">
    <thead>
    <tr class="border-light_blue divide-x border-b">
        <x-ui.table-th class="text-center" baseClass="min-w-24 w-24 text-dark_gray font-normal bg-white">
            <span id="processCounter" class="hidden opacity-50 font-normal text-xs"></span>
        </x-ui.table-th>

        @foreach($period as $date)
            @php
                $date = Carbon\Carbon::parse($date);
            @endphp
            <x-ui.table-th class="text-center {{ $date->isToday() ? 'text-blue': 'text-dark_gray' }}"
                           baseClass="min-w-24 font-normal bg-white">
                <span class="block text-xs font-normal">{{$date->format('M Y')}}</span>
                <span class="block text-xl">{{$date->format('j')}}</span>
                <span class="block text-xs font-normal">{{$date->format('D')}}</span>
            </x-ui.table-th>
        @endforeach
    </tr>
    </thead>

    <x-ui.table-tbody>
        @foreach($tableData as $type => $accounts)
            <tr class="bg-blue text-white uppercase">
                <x-ui.table-td padding="py-1 pr-2 pl-4" baseClass="text-white" attr="colspan={{$range+1}}">
                    {{ucfirst($type)}} Accounts
                </x-ui.table-td>
            </tr>
            @if($type == 'revenue')
                @foreach($accounts as $id => $data)
                    <tr class="bg-indigo-100 hover:bg-yellow-100 border-light_blue divide-x">
                        <x-ui.table-td padding="p-1 pl-2" baseClass="text-dark_gray">{{$data['name']}}</x-ui.table-td>
                        {{--                        <td class="border border-gray-300 whitespace-nowrap pl-2">{{$data['name']}}</td>--}}
                        @foreach($period as $date)
                            <x-ui.table-td class="text-right" padding="p-0" attr="disabled">
                                <input
                                    class="px-2 py-1 text-right bg-transparent border-none w-full"
                                    id="account_{{$id}}_{{$date->format('Y-m-d')}}"
                                    type="text" value="{{number_format($data[$date->format('Y-m-d')], 0, '.', '')}}" disabled/>
                            </x-ui.table-td>
                        @endforeach
                    </tr>
                    @foreach($data as $key => $ext_data)
                        @if(is_array($ext_data))
                            <tr class="hover:bg-yellow-100 border-light_blue divide-x">
                                <x-ui.table-td padding="p-1 pr-2 pl-6"
                                               baseClass="text-dark_gray whitespace-nowrap">{{$ext_data['name']}}</x-ui.table-td>
                                {{--                                <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">{{$ext_data['name']}}</td>--}}
                                @foreach($period as $date)
                                    <x-ui.table-td class="text-right hover:bg-yellow-100" padding="p-0" attr="disabled">
                                        <input
                                            class="px-2 py-1 w-full text-right bg-transparent border-0
                                            border-transparent outline-none
                                            focus:outline-none focus:ring-1 focus:shadow-none focus:bg-white"
                                            id="flow_{{$key}}_{{$date->format('Y-m-d')}}"
                                            type="text" value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}"/>
                                    </x-ui.table-td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            @else
                @foreach($accounts as $id => $data)
                    <tr class="bg-indigo-100 hover:bg-yellow-100 border-light_blue divide-x">
                        <x-ui.table-td class="text-left whitespace-nowrap" padding="p-1 pr-2 pl-4">
                            {{$data['name']}}
                        </x-ui.table-td>
                        @foreach($period as $date)
                            <x-ui.table-td class="text-right" padding="p-0">
                                <input class="px-2 py-1 w-full text-right bg-transparent border-none"
                                       type="text" value="{{number_format($data[$date->format('Y-m-d')], 0, '.', '')}}" disabled/>
                            </x-ui.table-td>
                        @endforeach
                    </tr>
                    @foreach($data as $key => $ext_data)
                        @if(is_array($ext_data))
                            @if($key == 'transfer')
                                <tr class="hover:bg-yellow-100 border-light_blue divide-x">
                                    <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-left whitespace-nowrap">
                                        Transfer In
                                    </x-ui.table-td>
                                    @foreach($period as $date)
                                        <x-ui.table-td padding="p-0" class="text-right">
                                            <input class="px-2 py-1 w-full text-right bg-transparent border-none"
                                                   type="text" value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}" disabled/>
                                        </x-ui.table-td>
                                    @endforeach
                                </tr>
                            @elseif($key == 'total')
                                <tr class="hover:bg-yellow-100 border-light_blue divide-x">
                                    <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-left whitespace-nowrap">
                                        Flow Total
                                    </x-ui.table-td>
                                    @foreach($period as $date)
                                        <x-ui.table-td padding="p-0" class="text-right">
                                            <input class="px-2 py-1 w-full text-right bg-transparent border-none"
                                                   type="text" value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}" disabled/>
                                        </x-ui.table-td>
                                    @endforeach
                                </tr>
                            @elseif(is_integer($key))
                                <tr class="bg-indigo-100 hover:bg-yellow-100 border-light_blue divide-x">
                                    <x-ui.table-td padding="p-1 pr-2 pl-4" class="text-left whitespace-nowrap">
                                        {{$ext_data['name']}}
                                    </x-ui.table-td>
                                    @foreach($period as $date)
                                        <x-ui.table-td padding="p-0" class="text-right hover:bg-yellow-200">
                                            <input class="px-2 py-1 w-full text-right bg-transparent border-0
                                                border-transparent outline-none
                                                focus:outline-none focus:ring-1 focus:shadow-none focus:bg-white"
                                                   id="flow_{{$key}}_{{$date->format('Y-m-d')}}"
                                                   type="text" value="{{number_format($ext_data[$date->format('Y-m-d')], 0, '.', '')}}"/>
                                        </x-ui.table-td>
                                    @endforeach
                                </tr>
                            @endif
                        @endif
                    @endforeach
                    <tr class="bg-light_blue">
                        <x-ui.table-td class="text-center h-1" padding="p-0"
                                       attr="colspan={{$range+1}}"></x-ui.table-td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </x-ui.table-tbody>
</x-ui.table-table>
