<x-app-layout>

    <x-slot name="header">
        {{ __('Dev Mail List') }}
    </x-slot>

    <x-ui.main>
        <x-ui.table-table>
            <x-ui.table-caption>
                List of emails for developer rendering
            </x-ui.table-caption>
            <thead>
                <tr class="border-t border-b border-light_blue">
                    <x-ui.table-th padding="pl-12 pr-2 py-4">Name</x-ui.table-th>

                    <x-ui.table-th>Mail Notes</x-ui.table-th>

                    <x-ui.table-th class="pr-12 text-center">Link</x-ui.table-th>
                </tr>
            </thead>

            <x-ui.table-tbody>
                @foreach ($emails as $email)
                <tr>
                    <x-ui.table-td class="pl-12">{{$email['name']}}</x-ui.table-td>
                    <x-ui.table-td>{{$email['notes']}}</x-ui.table-td>
                    <x-ui.table-td class="pr-12 text-center"><x-ui.button-normal href="/mail/{{$email['url']}}">Link</x-ui.button-normal></x-ui.table-td>
                </tr>
                @endforeach
            </x-ui.table-tbody>

        </x-ui.table-table>
    </x-ui.main>
</x-app-layout>
