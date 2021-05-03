<x-app-layout>
    <x-slot name="header">
        {{ __('Balance Entry') }}
    </x-slot>

    <x-ui.main>

        @if (\Session::has('success'))
        <div class="px-3 py-3 mb-4 border rounded bg-green-200 border-green-300 text-green-800 opacity-0 opacity-100">
            <h4 class="">Success!</h4>
            <p>{!! \Session::get('success') !!}</p>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <x-ui.table-table>
            <x-ui.table-caption>
                <span>Enter Account Balances for {{$business->name}}</span>
            </x-ui.table-caption>
            <x-forms.account-entry :business="$business" />
        </x-ui.table-table>
        <div class="py-4"></div>
    </x-ui.main>

</x-app-layout>
