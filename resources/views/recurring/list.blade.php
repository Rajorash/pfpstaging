<x-app-layout>

    <x-slot name="header">
        {{ __('Recurring Transactions') }}
    </x-slot>

    <x-ui.main>

        @if (session('status'))
            <div
                class="p-3 mx-12 mt-8 text-base text-indigo-500 bg-indigo-100 border border-indigo-300 rounded-lg status">
                {{ session('status') }}
            </div>
        @endif

        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 pl-72 pr-12 relative">

                <x-slot name="left">
                    <div class="absolute left-12 top-12">
                        <x-ui.button-normal
                            href="{{url('business/'.$bankAccount->business->id.'/accounts')}}">
                            <x-icons.chevron-left :class="'h-3 w-auto'"/>
                            <span class="ml-2">{{__('Go back')}}</span>
                        </x-ui.button-normal>
                    </div>
                </x-slot>

                {{ __('Flow') }}
                &quot;{{$accountFlow->label}}&quot;
                <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
                {{ __('Recurring Transactions') }}

                <x-slot name="right">
                    <x-ui.button-normal
                        href="{{route('recurring-create', ['account'=>$bankAccount, 'flow'=> $accountFlow])}}">
                        <x-icons.add/>
                        <span class="ml-2">{{__('Add new')}}</span>
                    </x-ui.button-normal>
                </x-slot>
            </x-ui.table-caption>
            <thead>
            <tr class="border-t border-b border-light_blue">
                <x-ui.table-th padding="pl-12 pr-2 py-4">{{__('Title of recurring')}}</x-ui.table-th>
                <x-ui.table-th>{{__('Date of start')}}</x-ui.table-th>
                <x-ui.table-th>{{__('Date of end')}}</x-ui.table-th>
                <x-ui.table-th>{{__('Description of Transactions')}}</x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th padding="pl-2 pr-12 py-4"></x-ui.table-th>
            </tr>
            </thead>

            <x-ui.table-tbody>
                @foreach ($recurringTransactions as $row)
                    <tr>
                        <x-ui.table-td padding="pl-12 pr-2 py-4">
                            {{$row->title}}
                        </x-ui.table-td>
                        <x-ui.table-td padding="pl-2 pr-2 py-4">
                            {{Carbon\Carbon::parse($row->date_start)->format('M d, Y')}}
                        </x-ui.table-td>
                        <x-ui.table-td padding="pl-2 pr-2 py-4">
                            @if($row->date_end)
                                {{Carbon\Carbon::parse($row->date_end)->format('M d, Y')}}
                            @endif
                        </x-ui.table-td>
                        <x-ui.table-td padding="pl-2 pr-2 py-4">
                            {{$row->description}}
                        </x-ui.table-td>

                        <x-ui.table-td padding="pl-2 pr-2 py-4">
                            <x-ui.button-small
                                href="{{route('recurring-edit', ['account' => $bankAccount, 'flow' => $accountFlow, 'recurring' => $row])}}">
                                <x-icons.edit class="w-3 h-auto mr-2"/>
                                {{__('Edit')}}
                            </x-ui.button-small>
                        </x-ui.table-td>

                        <x-ui.table-td padding="pl-2 pr-12 py-4">
                            <form class="inline-block"
                                  action="{{url('/accounts/'.$bankAccount->id.'/flow/'.$accountFlow->id.'/recurring/'.$row->id.'/delete')}}"
                                  method="POST">
                                @method('DELETE')
                                @csrf
                                <x-ui.button-small background="bg-red-900 hover:bg-dark_gray2"
                                                   class="w-auto h-6 text-white border-transparent hover:text-white"
                                                   attr="title=Delete" type="button">
                                    <x-icons.delete class="w-3 h-auto mr-2"/>
                                    {{__('Delete')}}
                                </x-ui.button-small>
                            </form>
                        </x-ui.table-td>
                    </tr>
                @endforeach
            </x-ui.table-tbody>

        </x-ui.table-table>

    </x-ui.main>


</x-app-layout>
