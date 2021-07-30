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
        <tr class="border-light_blue border-t border-b">
            <x-ui.table-th padding="pl-12 pr-2 py-4">Account</x-ui.table-th>
            <x-ui.table-th>Flows</x-ui.table-th>
            <x-ui.table-th>Edit Account</x-ui.table-th>
        </tr>
        </thead>

        <x-ui.table-tbody>
            @forelse($accounts as $acc)
                <tr>
                    <x-ui.table-td class="whitespace-nowrap align-top" padding="pl-12 pr-2 py-4">
                        <div class="text-{{strtolower($acc->type)}} text-2xl">{{ $acc->name }}</div>
                        <div class="text-white font-normal inline-block py-0 px-3 rounded-lg bg-{{strtolower($acc->type)}}">{{ $acc->type }}</div>
                    </x-ui.table-td>

                    <x-ui.table-td class="align-top">
                        <div class="pb-2 text-lg">Account Flows</div>
                        @forelse($acc->flows as $flow)
                            <div class="table">
                                <div class="table-row">
                                    <div
                                        class="table-cell pb-2 text-sm pr-4 text-{{$flow->isNegative() ? 'red-500' : 'green' }}">
                                        {{ $flow->label }}
                                    </div>
                                    <div class="table-cell">
                                        <x-ui.button-small attr="title=Edit"
                                                           class="w-auto h-6 mr-4 text-light_purple" padding="py-1 px-1"
                                                           background="bg-transparent hover:bg-transparent border-transparent border-transparent"
                                                           href="{{url('/accounts/'.$acc->id.'/flow/'.$flow->id.'/edit')}}">
                                            <x-icons.edit class="w-3 h-auto"/>
                                        </x-ui.button-small>
                                    </div>
                                    <div class="table-cell pb-2 w-28">
                                        <form class="inline-block"
                                              action="{{url('/accounts/'.$acc->id.'/flow/'.$flow->id)}}"
                                              method="POST">
                                            @method('DELETE')
                                            @csrf
                                            <x-ui.button-small background="bg-transparent hover:bg-transparent"
                                                               padding="py-1 px-1"
                                                               class="w-auto h-6 mr-4 text-red-300 hover:text-red-700 border-transparent"
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
                                <div class="table-cell pb-2 w-28 pr-4">
                                    <x-ui.button-small background="bg-green hover:bg-dark_gray2"
                                                       href="{{url('/accounts/'.$acc->id.'/create-flow')}}">
                                        <x-icons.add class="w-3 h-auto mr-2"/>
                                        Add Flow
                                    </x-ui.button-small>
                                </div>
                                <div class="table-cell pb-2 w-16 pr-4">
                                    <x-ui.button-small
                                        href="{{url(Request::path().'/'.$acc->id.'/edit')}}">
                                        <x-icons.edit class="w-3 h-auto mr-2"/>
                                        Edit
                                    </x-ui.button-small>
                                </div>
                                <div class="table-cell pb-2 w-16">

                                    @if($confirmingId === $acc->id)
                                        <x-ui.button-small background="bg-red-500 hover:bg-dark_gray2"
                                                           type="button"
                                                           wire:click="deleteAccount({{$acc->id}})">
                                            <x-icons.confirm class="w-3 h-auto mr-2"/>
                                            Confirm&nbsp;Delete?
                                        </x-ui.button-small>
                                    @else
                                        <x-ui.button-small background="bg-red-900 hover:bg-dark_gray2"
                                                           type="button"
                                                           wire:click="confirmDeleteAccount({{$acc->id}})">
                                            <x-icons.delete class="w-3 h-auto mr-2"/>
                                            Delete
                                        </x-ui.button-small>
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
