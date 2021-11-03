<div class="livewire-wrapper">
    <x-ui.table-table>
        <x-ui.table-caption>
            <span>{{$business->name}} {{__('Bank Accounts')}}</span>

            <x-slot name="right">
                <x-ui.button-normal href="{{route('accounts.create', ['business' => $business])}}">
                    <x-icons.document-add/>
                    <span class="ml-2">{{__('New Account')}}</span>
                </x-ui.button-normal>
            </x-slot>

        </x-ui.table-caption>

        <thead>
        <tr class="border-t border-b border-light_blue">
            <x-ui.table-th padding="pl-12 pr-2 py-4">{{__('Account')}}</x-ui.table-th>
            <x-ui.table-th>{{__('Flows')}}</x-ui.table-th>
            <x-ui.table-th>{{__('Edit Account')}}</x-ui.table-th>
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
                        <div class="pb-2 text-lg">{{__('Account Flows')}}</div>
                        @forelse($acc->flows as $flow)
                            <div class="table w-full mb-2">
                                <div class="table-row w-full text-sm hover:bg-gray-100">
                                    <div
                                        class="table-cell px-2 pb-1 rounded-tl-lg rounded-bl-lg text-{{$flow->isNegative() ? 'red-500' : 'green' }}">
                                        {{ $flow->label }} ({{ $flow->certainty }}%)
                                    </div>
                                    {{-- @if(auth()->user()->isAdvisor() || auth()->user()->isClient())
                                        <div class="table-cell w-10 px-2 pb-1">
                                            <div class="flex">
                                                @php
                                                    $recurringTransactionsCount = count($flow->recurringTransactions);
                                                @endphp
                                                @if($recurringTransactionsCount)
                                                    <div class="leading-6"
                                                         title="{{__('Flow contains').' '.$recurringTransactionsCount
                                                            .' '.Str::plural('recurring task', $recurringTransactionsCount)}}">
                                                        {{$recurringTransactionsCount}}</div>
                                                @endif
                                                <x-ui.button-small title="Recurring transactions"
                                                                   class="w-auto h-6 text-green hover:opacity-100 opacity-40"
                                                                   background="bg-transparent hover:bg-transparent border-transparent border-transparent"
                                                                   href="{{url('/accounts/'.$acc->id.'/flow/'.$flow->id.'/recurring')}}">
                                                    <x-icons.recurring class="w-3 h-auto"/>
                                                </x-ui.button-small>
                                            </div>
                                        </div>
                                    @endif --}}
                                    <div class="table-cell w-10 px-2 pb-1">
                                        <x-ui.button-small title="Edit"
                                                           class="w-auto h-6 text-light_purple hover:text-purple-700"
                                                           background="bg-transparent hover:bg-transparent border-transparent border-transparent"
                                                           href="{{url('/accounts/'.$acc->id.'/flow/'.$flow->id.'/edit')}}">
                                            <x-icons.edit class="w-3 h-auto"/>
                                        </x-ui.button-small>
                                    </div>
                                    <div class="table-cell w-10 px-2 pb-1 rounded-tr-lg rounded-br-lg">
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
                            <div>{{__('No flows added.')}}</div>
                        @endforelse
                    </x-ui.table-td>

                    <x-ui.table-td class="align-top">
                        <div class="table">
                            <div class="table-row">
                                <div class="table-cell pb-2 pr-4 w-28">
                                    <x-ui.button-small background="bg-green hover:bg-dark_gray2"
                                                       href="{{url('/accounts/'.$acc->id.'/create-flow')}}">
                                        <x-icons.add class="w-3 h-auto mr-2"/>
                                        {{__('Add Flow')}}
                                    </x-ui.button-small>
                                </div>
                                <div class="table-cell w-16 pb-2 pr-4">
                                    <x-ui.button-small
                                        href="{{route('accounts.edit', [
                                            'business' => $business,
                                            'account' => $acc
                                            ])}}">
                                        <x-icons.edit class="w-3 h-auto mr-2"/>
                                        {{__('Edit')}}
                                    </x-ui.button-small>
                                </div>
                                <div class="table-cell w-16 pb-2">
                                    @if( $acc->isDeletable() )

                                        <div class="flex flex-inline">
                                            <x-ui.button-small
                                                background="bg-red-900 hover:bg-dark_gray2"
                                                type="button"
                                                wire:click="confirmDeleteAccount({{$acc->id}})">
                                                <x-icons.delete class="w-3 h-auto mr-2"/>
                                                {{__('Delete')}}
                                            </x-ui.button-small>
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
                        {{__('No accounts created.')}}
                    </x-ui.table-td>
                </tr>
            @endforelse
        </x-ui.table-tbody>
    </x-ui.table-table>
    @if ($confirmingId)
        <x-jet-confirmation-modal wire:model="confirmingId">
            <x-slot name="title">
                {{__('Confirm Account Deletion?')}}
            </x-slot>
            <x-slot name="content">
                {{__('Are you certain you wish to delete this account, all flows and data will also be removed.')}}
            </x-slot>
            <x-slot name="footer">
                <div class="inline-flex space-x-4">
                    <x-ui.button-secondary
                        type="button"
                        background="bg-gray-500 hover:bg-gray-800"
                        wire:click="closeModal()">
                        {{__('Cancel')}}
                    </x-ui.button-secondary>
                    <x-ui.button-danger
                        type="button"
                        background="bg-red-500 hover:bg-red-800"
                        wire:click="deleteAccount()">
                        <x-icons.delete class="w-3 h-auto mr-2"/>
                        {{__('Confirm Delete?')}}
                    </x-ui.button-danger>
                </div>
            </x-slot>
        </x-jet-confirmation-modal>
    @endif
</div>
