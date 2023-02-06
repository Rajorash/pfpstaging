<div class="livewire-wrapper">
    <div class="table-row">
        <div class="table-cell w-1/4 text-lg text-left text-center text-black pt-4" id="tab1">
                <x-ui.button-secondary class="mr-4 {{ $tab1 ? 'bg-gray-300 text-gray-100 border-blue' : '' }} bg-slate-500 text-white  uppercase inline-flex items-center px-1 pt-1  border-transparent font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-blue focus:outline-none focus:text-gray-700 focus:blue transition duration-150 ease-in-out"  wire:click="$emit('checktab1')" type="button">
                            {{__('MANUAL ENTRY')}}
                </x-ui.button-secondary>
        </div>
        <div class="table-cell w-1/4 text-lg text-left text-center text-black pt-4" id="tab2">
                <x-ui.button-secondary class="{{ $tab2 ? 'bg-gray-300 text-gray-100 border-blue' : '' }} uppercase inline-flex items-center px-1 pt-1  border-transparent font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-blue focus:outline-none focus:text-gray-700 focus:blue transition duration-150 ease-in-out"  wire:click="$emit('checktab2')" type="button">
                            {{__('UPLOAD CSV')}}
                </x-ui.button-secondary>
        </div>
    </div>


    @if($tab1)


    <div>
        <form wire:submit.prevent="store">
            <div class="table w-full mt-10">

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Label') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input id="label" class="w-full" type="text" name="label"
                                    wire:model.defer="label" wire:loading.attr="disabled"
                                    required autofocus/>
                        <x-jet-input-error for="label" class="mt-2"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                    Category
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <select name="catId" id="catId" wire:model="catId" class="w-full form-input border-light_blue
                                focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                rounded-md shadow-sm">
                            <option value="" >Select Category</option>
                            @foreach($category_name as $cat)
                                <option value="{{ $cat->id }}" >{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for="catId" class="mt-2"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Certainty') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input id="certainty" class="w-full" type="number"
                                    min="0" max="500" step="5"
                                    name="certainty"
                                    wire:model.defer="certainty"
                                    wire:loading.attr="disabled"
                                    required/>
                        <x-jet-input-error for="certainty" class="mt-2"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Flow Type') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        <input class="form-radio" type="radio" name="negative_flow" id="flow-in"
                            autocomplete="off"
                            wire:model.defer="negative_flow"
                            wire:loading.attr="disabled"
                            value="0"/>
                        <label for="flow-in">{{__('Positive')}}</label>
                        <input class="ml-4 form-radio" type="radio" name="negative_flow" id="flow-out"
                            autocomplete="off"
                            wire:model.defer="negative_flow"
                            wire:loading.attr="disabled"
                            value="1"
                        />
                        <x-jet-input-error for="negative_flow" class="mt-2"/>
                        <label for="flow-out">{{__('Negative')}}</label>
                    </div>
                </div>

            </div>

            <div class="table w-full mt-4">
                <div class="table-row">
                    @if ($modalMode && $flowId)
                        <div class="table-cell w-1/3 pb-4 text-left">
                            <x-ui.button-danger class="uppercase"
                                                wire:click="$emit('openModal', 'confirm-delete-modal', {{json_encode(['flowId' => $flowId, 'accountId' => $accountId, 'routeName' => $routeName])}})"
                                                type="button">
                                {{__('Delete Flow')}}
                            </x-ui.button-danger>
                        </div>
                    @endif

                    <div class="table-cell @if ($modalMode) w-2/3 @else w-full @endif  pb-4 text-right">
                        @if ($modalMode)
                            <x-ui.button-secondary class="mr-4 uppercase" wire:click="$emit('closeModal')" type="button">
                                {{__('Cancel')}}
                            </x-ui.button-secondary>
                        @endif

                        <x-ui.button-normal wire:loading.attr="disabled" class="uppercase" type="button">
                            @if($flowId)
                                {{__('Update Flow')}}
                            @else
                                {{__('Create Flow')}}
                            @endif
                        </x-ui.button-normal>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @elseif($tab2)


    <div class=" text-right text-black mt-4">
             <a href="{{url('/sampleflow.xlsx')}}" class="bg-blue-500 no-underline hover:underline ">Export Sample CSV</a>
    </div>
    <div>   
        
        <form wire:submit.prevent="import" method="POST" enctype="multipart/form-data">
            

            <div class="table w-full mt-10">
                <div class="table-row">
                    <div class="table-cell text-lg w-1/4 pb-4 text-left">
                                    {{ __('Import Flow Type') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input class="form-control block w-full px-3 py-2 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" wire:model="flowscsv" name="flowscsv" type="file" id="flowscsv"/>
                        <x-jet-input-error for="flowscsv" class="mt-2"/>
                        @if($errormessege != '') <span style="color: red;">{{ $errormessege }}</span> @endif
                        

                        @if (count($errors) > 0)
                            @foreach($errors->all() as $error)
                                <span style="color: red;">{{ $error }}</span>  <br>
                            @endforeach      
                        @endif


                    </div>
                </div>
            </div>
            
            <div class="table w-full mt-4">
                <div class="table-row">
                    <div class="table-cell @if ($modalMode) w-2/3 @else w-full @endif  pb-4 text-right">
                    @if ($modalMode)
                        <x-ui.button-secondary class="mr-4 uppercase" wire:click="$emit('closeModal')" type="button">
                            {{__('Cancel')}}
                        </x-ui.button-secondary>
                    @endif
                    <x-ui.button-normal wire:loading.attr="disabled" class="uppercase" type="button">
                            {{__('Import')}}
                    </x-ui.button-normal>
                    </div>
                </div>
            </div>
        </form>

    </div>

    @endif

</div>

