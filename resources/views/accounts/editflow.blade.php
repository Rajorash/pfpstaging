<x-app-layout>

    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Update Flow
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-ui.main>
        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-72 relative">
                Update Flow For {{$account->name}}

                <x-slot name="left">
                    <div class="absolute left-12 top-12">
                        <x-ui.button-normal href="{{url('/business/'.$business->id.'/accounts')}}">
                            <x-icons.chevron-left :class="'h-3 w-auto'"/>
                            <span class="ml-2">Go back</span>
                        </x-ui.button-normal>
                    </div>
                </x-slot>

            </x-ui.table-caption>
            <x-ui.table-tbody>
                <tr>
                    <x-ui.table-td class="text-center bg-gray-100" padding="px-72 py-4">
                        <form method="POST" action="{{url('/accounts/'.$account->id.'/flow/'.$flow->id)}}">
                            @csrf
                            @method('PUT')
                            <div class="table w-full mt-10">

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Label') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="label" class="w-full" type="text" name="label"
                                                     value="{{$flow->label}}" required autofocus/>
                                        <x-jet-input-error for="label" class="mt-2"/>
                                    </div>
                                </div>

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Flow Type') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4 text-left">
                                        <label
                                            class="mr-4">
                                            <input class="form-radio" type="radio" name="flow-direction" id="flow-in"
                                                   autocomplete="off"
                                                   value="0" {{ $flow->isNegative() ? '' : 'checked' }}>
                                            Positive
                                        </label>
                                        <label
                                            class="">
                                            <input class="form-radio" type="radio" name="flow-direction" id="flow-out"
                                                   autocomplete="off"
                                                   value="1" {{ $flow->isNegative() ? 'checked' : '' }}>
                                            Negative
                                        </label>
                                        <x-jet-input-error for="flow-direction" class="mt-2"/>
                                    </div>
                                </div>

                            </div>

                            <div class="table w-full mt-4">
                                <div class="table-row">
                                    <div class="table-cell w-full pb-4 text-right">
                                        <x-ui.button-normal class="uppercase" type="button">
                                            Update Flow
                                        </x-ui.button-normal>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>
    </x-ui.main>


</x-app-layout>
