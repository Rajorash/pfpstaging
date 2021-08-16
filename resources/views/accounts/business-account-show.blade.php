<div class="livewire-wrapper">
    <x-ui.table-table>
        <x-ui.table-caption>
            <span>{{$business->name}} Bank Accounts</span>

            <x-slot name="right">
                <x-ui.button-normal href="{{url(Request::path().'/create')}}">
                    <x-icons.document-add/>
                    <span class="ml-2">New Account</span>
                </x-ui.button-normal>
            </x-slot>

        </x-ui.table-caption>

        <thead>
        <tr class="border-t border-b border-light_blue">
            <x-ui.table-th padding="pl-12 pr-2 py-4">Account</x-ui.table-th>
            <x-ui.table-th>Flows</x-ui.table-th>
            <x-ui.table-th>Edit Account</x-ui.table-th>
        </tr>
        </thead>

        <x-ui.table-tbody>
            @forelse($accounts as $acc)
                <tr>
                    <x-ui.table-td class="align-top whitespace-nowrap" padding="pl-12 pr-2 py-4">
                        <div class="text-{{strtolower($acc->type)}} text-2xl">{{ $acc->name }}</div>
                        <div
                            class="text-white font-normal inline-block py-0 px-3 rounded-lg bg-{{strtolower($acc->type)}}">{{ $acc->type }}</div>
                    </x-ui.table-td>

                    <x-ui.table-td class="align-top">
                        <div class="pb-2 text-lg">Account Flows</div>
                        @forelse($acc->flows as $flow)
                            <div class="table w-full mb-2">
                                <div class="table-row hover:bg-gray-100 w-full text-sm">
                                    <div
                                        class="table-cell px-2 pb-1 rounded-tl-lg rounded-bl-lg text-{{$flow->isNegative() ? 'red-500' : 'green' }}">
                                        {{ $flow->label }}
                                    </div>
                                    @if(auth()->user()->isAdvisor() || auth()->user()->isClient())
                                        <div class="table-cell px-2 pb-1 w-10">
                                            <div class="flex">
                                                @if(count($flow->recurringTransactions))
                                                    <div class="leading-6"
                                                         title="{{__('Flow contains').' '.count($flow->recurringTransactions).' '.Str::plural('recurring task', count($flow->recurringTransactions))}}">{{count($flow->recurringTransactions)}}</div>
                                                @endif
                                                <x-ui.button-small title="Recurring transactions"
                                                                   class="w-auto h-6 text-green hover:opacity-100 opacity-40"
                                                                   background="bg-transparent hover:bg-transparent border-transparent border-transparent"
                                                                   href="{{url('/accounts/'.$acc->id.'/flow/'.$flow->id.'/recurring')}}">
                                                    <x-icons.recurring class="w-3 h-auto"/>
                                                </x-ui.button-small>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="table-cell px-2 pb-1 w-10">
                                        <x-ui.button-small title="Edit"
                                                           class="w-auto h-6 text-light_purple hover:text-purple-700"
                                                           background="bg-transparent hover:bg-transparent border-transparent border-transparent"
                                                           href="{{url('/accounts/'.$acc->id.'/flow/'.$flow->id.'/edit')}}">
                                            <x-icons.edit class="w-3 h-auto"/>
                                        </x-ui.button-small>
                                    </div>
                                    <div class="table-cell px-2 pb-1 rounded-tr-lg rounded-br-lg w-10">
                                        <form class="inline-block"
                                              action="{{url('/accounts/'.$acc->id.'/flow/'.$flow->id)}}"
                                              method="POST">
                                            @method('DELETE')
                                            @csrf
                                            <x-ui.button-small background="bg-transparent hover:bg-transparent"
                                                               class="w-auto h-6 text-red-300 border-transparent hover:text-red-700"
                                                               attr="title=Delete" type="button">
                                                <x-icons.delete class="w-3 h-auto"/>
                                            </x-ui.button-small>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="">No flows added.</div>
                        @endforelse
                    </x-ui.table-td>

                    <x-ui.table-td class="align-top">
                        <div class="table">
                            <div class="table-row">
                                <div class="table-cell pb-2 pr-4 w-28">
                                    <x-ui.button-small background="bg-green hover:bg-dark_gray2"
                                                       href="{{url('/accounts/'.$acc->id.'/create-flow')}}">
                                        <x-icons.add class="w-3 h-auto mr-2"/>
                                        Add Flow
                                    </x-ui.button-small>
                                </div>
                                <div class="table-cell w-16 pb-2 pr-4">
                                    <x-ui.button-small
                                        href="{{url(Request::path().'/'.$acc->id.'/edit')}}">
                                        <x-icons.edit class="w-3 h-auto mr-2"/>
                                        Edit
                                    </x-ui.button-small>
                                </div>
                                <div class="table-cell w-16 pb-2">
                                    @if( $acc->isDeletable() )

                                        <div class="flex flex-inline">
                                            @if($confirmingId === $acc->id)
                                                <x-ui.button-small
                                                    background="bg-red-500 hover:bg-dark_gray2"
                                                    type="button"
                                                    wire:click="deleteAccount({{$acc->id}})">
                                                    <x-icons.confirm class="w-3 h-auto mr-2"/>
                                                    Confirm&nbsp;Delete?
                                                </x-ui.button-small>
                                            @else
                                                <x-ui.button-small
                                                    background="bg-red-900 hover:bg-dark_gray2"
                                                    type="button"
                                                    wire:click="confirmDeleteAccount({{$acc->id}})">
                                                    <x-icons.delete class="w-3 h-auto mr-2"/>
                                                    Delete
                                                </x-ui.button-small>
                                            @endif
                                        </div>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-ui.table-td>
                </tr>
            @empty
                <tr>
                    <x-ui.table-td attr="colspan=3" class="text-center">
                        No accounts created.
                    </x-ui.table-td>
                </tr>
            @endforelse
        </x-ui.table-tbody>
    </x-ui.table-table>
</div>
