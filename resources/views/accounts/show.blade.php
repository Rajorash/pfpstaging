<x-app-layout>
    <x-slot name="header">
        {{$business->name}} <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/> Accounts
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <x-ui.card bodypadding="0">

        <x-slot name="header">
            <h2 class="text-lg leading-6 font-medium text-black">{{$business->name}} Bank Accounts</h2>
            <a href="/{{ Request::path() }}/create"
               class="group flex items-center text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline bg-green text-white hover:bg-dark_gray">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     class="w-4 h-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                New Account
            </a>
        </x-slot>

        <x-ui.table tableId="accountsTable">
            <thead class="bg-gray-50">
            <x-ui.th :class="'border-l-0'">Account</x-ui.th>
            <x-ui.th>Flows</x-ui.th>
            <x-ui.th :class="'border-r-0'">Edit Account</x-ui.th>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($accounts as $acc)
                <tr class="align-top">
                    <x-ui.td>
                        <div class="px-2 py-1">
                            <strong class="uppercase block">{{ $acc->name }}</strong>
                            <em class="block text-gray-500">{{ $acc->type }}</em>
                        </div>
                    </x-ui.td>
                    <x-ui.td>
                        <div class="">
                            <strong class="block">Account Flows</strong>
                            @forelse($acc->flows as $flow)
                                <div
                                    class="flex justify-between items-center pt-2  text-{{$flow->isNegative() ? 'red-500' : 'green-600' }}">
                                    {{ $flow->label }}
                                    <span class="inline-block text-right"><a
                                            href="/accounts/{{$acc->id}}/flow/{{$flow->id}}/edit"
                                            class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-yellow-500 text-white hover:bg-yellow-600 mr-1">Edit Flow</a>
                                        <form class="inline" action="/accounts/{{$acc->id}}/flow/{{$flow->id}}"
                                              method="POST">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit"
                                                    class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-red-600 text-white hover:bg-red-700">Delete Flow</button>
                                        </form>
                                    </span>
                                </div>
                            @empty
                                <div class="py-2 pl-4 pr-2">No flows added.</div>
                            @endforelse
                        </div>
                    </x-ui.td>
                    <x-ui.td>
                            <span class="inline-block text-right">
                                <a href="/accounts/{{ $acc->id }}/create-flow"
                                   class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-green-500 text-white hover:bg-green-600 mr-1">+ Flow</a>
                                <a href="/{{ Request::path() }}/{{$acc->id}}/edit"
                                   class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-yellow-500 text-white hover:bg-yellow-600 mr-1">Edit</a>
                                <form class="inline" action="/{{ Request::path() }}/{{$acc->id}}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit"
                                            class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-red-600 text-white hover:bg-red-700">Delete</button>
                                </form>
                            </span>
                    </x-ui.td>

                </tr>
            @empty
                <x-ui.tr>
                    <x-ui.td>
                        <div class="flex justify-between items-center px-2 py-1">
                            <strong>No accounts created.</strong>
                        </div>
                    </x-ui.td>
                </x-ui.tr>
            @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.card>

</x-app-layout>
