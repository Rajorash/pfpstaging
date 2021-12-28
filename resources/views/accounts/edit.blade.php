<x-app-layout>

    <x-slot name="titleHeader">
        {{$business->name}}
        &gt;
        {{ __('Update Account') }}
    </x-slot>

    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        {{ __('Update Account') }}
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>


    <x-ui.main>
        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-48 lg:px-52 xl:px-60 2xl:px-72 relative relative">
                {{__('Update Account For')}} {{$business->name}}

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
                    <x-ui.table-td class="text-center bg-gray-100"
                                   padding="px-12 sm:px-24 md:px-36 lg:px-48 xl:px-60 2xl:px-72 py-4">
                        <form method="POST"
                              action="{{url('/business/'.$account->business_id.'/accounts/'.$account->id)}}">
                            @csrf
                            @method('PUT')
                            <div class="table w-full mt-10">

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Label') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="name" class="w-full" type="text" name="name"
                                                     value="{{$account->name}}" required autofocus/>
                                        <x-jet-input-error for="name" class="mt-2"/>
                                    </div>
                                </div>

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Account Type') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <select name="type" id="type" class="form-select rounded p-2 my-0 w-full
                                                form-input border-light_blue
                                                focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                                @error('type') bg-red-700 @enderror">
                                            <option value="">Select your account type</option>
                                            @foreach (App\Models\BankAccount::type_list() as $account_index => $account_type)
                                                <option
                                                    value="{{ $account_index }}"{{ $account_type == $curr_type ? ' selected' : '' }}>{{ $account_type }}</option>
                                            @endforeach
                                        </select>
                                        <x-jet-input-error for="type" class="mt-2"/>
                                    </div>
                                </div>


                            </div>

                            <div class="table w-full mt-4">
                                <div class="table-row">
                                    <div class="table-cell w-full pb-4 text-right">
                                        <x-ui.button-normal class="uppercase" type="button">
                                            {{__('Update Account')}}
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
