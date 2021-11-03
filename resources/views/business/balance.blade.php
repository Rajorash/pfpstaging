<x-app-layout>
    <x-slot name="header">
        <x-cta-workflow :business="$business" :step="'balance'" />

        {{$business->name }}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        {{ __('Change balance')}}
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-ui.main>
        <x-ui.table-table>
            <x-ui.table-caption class="relative px-48 pt-12 pb-6 lg:px-52 xl:px-60 2xl:px-72">
                {{$business->name}}, {{__('change balance for')}}: {{ \Carbon\Carbon::parse($today)->format('l, d F Y')}}

                <x-slot name="left">
                    <div class="absolute left-12 top-12">
                        <x-ui.button-normal href="{{route('businesses')}}">
                            <x-icons.chevron-left :class="'h-3 w-auto'"/>
                            <span class="ml-2">{{__('Go back')}}</span>
                        </x-ui.button-normal>
                    </div>
                </x-slot>

                @php
                    $checkLicense = $business->license->checkLicense;
                @endphp

                @if(!$checkLicense)
                    <div
                        class="font-bold text-center text-red-500">{{__('License is inactive. Edit data forbidden.')}}</div>
                @endif

                @if (session('status'))
                    <div class="mt-2 text-sm text-indigo-500 status">
                        {{ session('status') }}
                    </div>
                @endif
            </x-ui.table-caption>
            <x-ui.table-tbody>
                <tr>
                    <x-ui.table-td class="text-center bg-gray-100"
                                   padding="px-12 sm:px-24 md:px-36 lg:px-48 xl:px-60 2xl:px-72 py-4">
                        <form method="post"
                              action="{{route('balanceStore.business', ['business' => $business])}}">
                            @csrf
                            <div class="table w-full mt-10">
                                <div class="table-row">
                                    <div class="table-cell w-1/5 pb-4 font-semibold text-left uppercase">Account</div>
                                    <div class="table-cell w-1/5 px-4 pb-4 font-semibold text-right text-gray-600 uppercase">Expected</div>
                                    <div class="table-cell w-3/5 pb-4 font-semibold uppercase">Input Actual Balance</div>
                                </div>
                                @foreach ($balances as $balance)
                                    <div class="table-row">
                                        <div class="table-cell w-1/5 pb-4 text-left">{{$balance['title']}}</div>
                                        <div class="table-cell w-1/5 px-4 pb-4 text-right text-gray-600">{{number_format($balance['amount'], 0)}}</div>
                                        <div class="table-cell w-3/5 pb-4">
                                            <x-jet-input
                                                name="balance[{{$balance['id']}}]"
                                                type="text"
                                                class="w-full text-right"
                                                autocomplete="off"
                                                value="{{number_format($balance['amount'], 0)}}"
                                            />
                                        </div>
                                    </div>

                                @endforeach
                                <div class="table-row">
                                    @if($checkLicense)
                                        <div class="table-cell w-1/5 pb-4 text-left"></div>
                                        <div class="table-cell w-1/5 px-4 pb-4 text-left"></div>
                                        <div class="table-cell w-3/5 pb-4 text-left">
                                            <x-ui.button-normal class="uppercase" type="button"
                                                                onclick="this.disabled=true;this.form.submit();this.innerText='...calculating';">
                                                {{__('Save changes and recalculate flow')}}
                                            </x-ui.button-normal>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>
    </x-ui.main>

</x-app-layout>
