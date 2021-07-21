<div class="livewire-wrapper">
    <div class="table w-full">
        <div class="table-row">
            <div class="table-cell w-full text-left">
                <div class="table w-full float-left">
                    <div class="table-row">
                        <div class="table-cell pb-2 w-1/3">{{__('Name')}}</div>
                        <div class="table-cell pb-2 w-2/3">{{ $user->name }}</div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell pb-2">{{__('Current count of licenses')}}</div>
                        <div class="table-cell pb-2">
                            {{$licensesCounter}}
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell pb-2">{{__('Assigned Licenses')}}</div>
                        <div class="table-cell pb-2">
                            @if(count($user->licenses))
                                {{count($user->licenses)}}
                            @else
                                <span class="text-red-700">No Licenses</span>
                            @endif
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell pb-2 bg-red">{{__('Available licenses')}}</div>
                        <div class="table-cell pb-2">
                            @if($licensesCounter - count($user->licenses) < 0)
                                <x-ui.badge
                                    background="bg-red-700">{{$licensesCounter - count($user->licenses)}}</x-ui.badge>
                            @else
                                <x-ui.badge>{{$licensesCounter - count($user->licenses)}}</x-ui.badge>
                            @endif
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell pb-2"></div>
                        <div class="table-cell pb-2">
                            <input type="checkbox" wire:model="allowEdit"
                                   id="allowEdit"/>
                            <label for="allowEdit" class="pl-2">Check it to allow edit count of Licenses</label>
                        </div>
                    </div>

                    <div class="table-row @if(!$allowEdit) hidden @endif">
                        <div class="table-cell pb-2">{{__('Set new licenses count')}}</div>
                        <div class="table-cell pb-2">
                            <x-jet-input
                                id="licensesCounter"
                                class="w-full"
                                type="number"
                                name="responsibility"
                                min="0"
                                step="1"
                                autofocus
                                wire:model.debounce.1s="licensesCounter"
                            />
                            <span class="text-sm">{{__('Change value and wait 2 seconds. Value will be save automatically')}}</span>
                            <x-jet-input-error for="licensesCounter" class="text-left mt-2"/>
                        </div>
                    </div>

                    <div class="table-row @if(!$licensesCounterMessage) hidden @endif">
                        <div class="table-cell pb-2"></div>
                        <div class="table-cell pb-2">
                            <x-ui.badge>{{$licensesCounterMessage}}</x-ui.badge>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @if (count($licensesCounterHistory))
        <x-ui.table-table>
            <x-ui.table-caption>
                <span>History</span>
            </x-ui.table-caption>
            <thead>
            <tr class="border-light_blue border-t border-b">
                <x-ui.table-th>Regional Admin</x-ui.table-th>
                <x-ui.table-th>Count of licenses</x-ui.table-th>
                <x-ui.table-th>Date</x-ui.table-th>
                @if(Auth::user()->isRegionalAdmin())
                    <x-ui.table-th></x-ui.table-th>
                @endif
            </tr>
            </thead>
            <x-ui.table-tbody>
                @foreach ($licensesCounterHistory as $row)
                    <tr>
                        {{$row->regionalAdminByAdvisor}}
                        <x-ui.table-td>
                            @if($row->regionalAdmin)
                                {{$row->regionalAdmin->name}}
                            @endif
                        </x-ui.table-td>
                        <x-ui.table-td class="text-right">
                            {{$row->licenses}}
                        </x-ui.table-td>
                        <x-ui.table-td>
                            {{Carbon\Carbon::parse($row->created_at)->diffForHumans()}}
                        </x-ui.table-td>
                    </tr>
                @endforeach
            </x-ui.table-tbody>
        </x-ui.table-table>
        <div class="m-6">
            @if ($licensesCounterHistory)
                {{ $licensesCounterHistory->links() }}
            @endif
        </div>
    @endif
</div>
