<div>
    <div class="pb-6 text-2xl text-black text-bold text-center text-red-700">
        @if ($flowMessage)
            {{ $flowMessage }}
            
            <div class="table-row">
                    <div class="table-cell mx-1 w-1/4 text-lg text-left pt-4 text-center text-black">
                        {{ __('Export CSV :') }}
                    </div>
                    <div class="table-cell w-3/4 text-lg text-left text-center text-black pt-4">
                        <a class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-blue focus:outline-none focus:text-gray-700 focus:blue transition duration-150 ease-in-out" href="{{route('allocations-new', ['business' => session('currentid')])}}">
                        {{__('Click here if you would like to export expense data')}} </a>
                    </div>
                </div>
                
                <div class="table-row">
                    <div class="table-cell mx-1 w-1/4 text-lg text-left pt-4 text-center text-black">
                    </div>
                    <div class="table-cell w-3/4 text-lg text-left text-center text-black pt-4">
                        <a class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-blue focus:outline-none focus:text-gray-700 focus:blue transition duration-150 ease-in-out ml-3" href="{{route('projection-view', ['business' => session('currentid')])}}">
                        {{__('Click here if you would like to export projection data')}} </a>
                    </div>
                </div>
        @else
            {{__('Error, Message not found')}}
        @endif
    </div>
    <form wire:submit.prevent="store">
    <div class="table w-full mt-4">
        <div class="table-row">
            <div class="table-cell w-1/2 pb-4 text-left">
                <x-ui.button-secondary class="mr-4 uppercase uncheckbox" wire:click="$emit('falseModal')" type="button">
                    {{__('Cancel')}}
                </x-ui.button-secondary>
            </div>
            <div class="table-cell w-1/2 pb-4 text-right">
                <x-ui.button-danger wire:loading.attr="disabled" wire:click="$emit('confirmDelete')" class="uppercase checkbox"
                                    type="button">
                    {{__('Yes, delete the Business')}}
                </x-ui.button-danger>
            </div>
        </div>
    </div>
</form>
</div>
