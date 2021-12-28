<x-app-layout>

    <x-slot name="titleHeader">
        @if(is_object($pipeline) && is_a($pipeline, 'App\Models\Pipelines'))
            {{ __('Edit Pipeline') }}
        @else
            {{ __('Add Pipeline') }}
        @endif
    </x-slot>

    <x-slot name="header">
        @if(is_object($pipeline) && is_a($pipeline, 'App\Models\Pipelines'))
            {{ __('Edit Pipeline') }}
        @else
            {{ __('Add Pipeline') }}
        @endif
    </x-slot>

    <x-ui.main>

        @if (session('status'))
            <div
                class="p-3 mx-12 mt-8 text-base text-indigo-500 bg-indigo-100 border border-indigo-300 rounded-lg status">
                {{ session('status') }}
            </div>
        @endif

        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-48 lg:px-52 xl:px-60 2xl:px-72 relative relative">
                {{ __('Add Pipeline for Business') }}
                &quot;{{$bankAccount->name}}&quot;

                <x-slot name="left">
                    <div class="absolute left-12 top-12">
                        <x-ui.button-normal
                            href="{{route('pipelines.list', ['business'=>$bankAccount])}}">
                            <x-icons.chevron-left :class="'h-3 w-auto'"/>
                            <span class="ml-2">{{__('Go back')}}</span>
                        </x-ui.button-normal>
                    </div>
                </x-slot>

            </x-ui.table-caption>
            <x-ui.table-tbody>
                <tr>
                    <x-ui.table-td class="text-center bg-gray-100"
                                   padding="px-12 sm:px-24 md:px-36 lg:px-48 xl:px-60 2xl:px-72 py-4">
                        <livewire:pipeline-livewire
                            :bankAccount="$bankAccount"
                            :pipeline="$pipeline"
                        />
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>

    </x-ui.main>


</x-app-layout>
