
<div class="livewire-wrapper">
    @if(Auth::user()->isAdvisor() || Auth::user()->isClient())

    <div class="table-row">
        <div class="table-cell w-1/4 text-lg text-left text-center text-black pt-4" id="tab1">
                <x-ui.button-secondary class="mr-4 {{ $tab1 ? 'bg-gray-300 text-gray-100 border-blue' : '' }} bg-slate-500 text-white  uppercase inline-flex items-center px-1 pt-1  border-transparent font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-blue focus:outline-none focus:text-gray-700 focus:blue transition duration-150 ease-in-out"  wire:click="$emit('checktab1')" type="button">
                            {{__('RECURRING ENTRY')}}
                </x-ui.button-secondary>
        </div>
        <div class="table-cell w-1/4 text-lg text-left text-center text-black pt-4" id="tab2">
                <x-ui.button-secondary class="{{ $tab2 ? 'bg-gray-300 text-gray-100 border-blue' : '' }} uppercase inline-flex items-center px-1 pt-1  border-transparent font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-blue focus:outline-none focus:text-gray-700 focus:blue transition duration-150 ease-in-out"  wire:click="$emit('checktab2')" type="button">
                            {{__('ONE-OFF ENTRY')}}
                </x-ui.button-secondary>
        </div>
    </div>

    @if($tab1)

    <div id="active_tab1">

            <form wire:submit.prevent="store">
                <div class="table w-full mt-10">

                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Value') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4">
                            <div class="flex">
                                <div class="w-1/12 text-3xl">
                                    {{$accountFlow->isNegative() ? '-' : '+'}}
                                </div>
                                <div class="w-11/12">
                                    <x-jet-input
                                        id="title"
                                        class="w-full"
                                        type="text"
                                        name="value"
                                        autocomplete="off"
                                        wire:model.lazy="value"
                                    />
                                </div>
                            </div>
                            <x-jet-input-error for="value" class="mt-2 text-left"/>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Date start') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4">
                            <x-jet-input
                                id="date_start"
                                class="w-full"
                                type="date"
                                name="date_start"
                                wire:model.debounce.2000ms="date_start"
                            />
                            <x-jet-input-error for="date_start" class="mt-2 text-left"/>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Date End') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4">
                            <x-jet-input
                                id="date_end"
                                class="w-full"
                                type="date"
                                name="date_end"
                                wire:model.lazy="date_end"
                            />
                            <x-jet-input-error for="date_end" class="mt-2 text-left"/>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Repeat every') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4">
                            <div class="flex">
                                <x-jet-input
                                    id="repeat_every_number"
                                    class="w-1/5"
                                    type="number"
                                    min="1" max="99" step="1"
                                    name="repeat_every_number"
                                    wire:model.lazy="repeat_every_number"
                                />

                                <select name="repeat_every_type" id="repeat_every_type" wire:model="repeat_every_type"
                                        class="w-4/5 rounded-md shadow-sm form-input border-light_blue focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach ($repeatTimeArray as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-jet-input-error for="repeat_every_type" class="mt-2 text-left"/>
                        </div>
                    </div>

                    @if($repeat_every_type == \App\Models\RecurringTransactions::REPEAT_WEEK)
                        <div class="table-row">
                            <div class="table-cell w-1/4 pb-4 text-left">
                                &nbsp;
                            </div>
                            <div class="table-cell w-3/4 pb-4">

                                <div class="flex">
                                    @foreach ($weekDaysArray as $key => $value)
                                        <div class="mr-2">
                                            <input type="checkbox" wire:model="repeat_rules_week_days"
                                                class="hidden radio_as_switcher"
                                                value="{{$key}}" id="week_days_{{$key}}"/>
                                            <label for="week_days_{{$key}}"
                                                title="{{$value}}">{{substr($value, 0, 2)}}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <x-jet-input-error for="repeat_rules_week_days" class="mt-2 text-left"/>
                            </div>
                        </div>
                    @endif

                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{__('Forecast')}}
                        </div>
                        <div class="table-cell w-3/4 pb-4 text-left">
                            @if (!empty($forecast))
                                <div class="max-h-60 overflow-y-scroll">
                                    @foreach ($forecast as $forecastDate => $forecastValue)
                                        <div>
                                            <b>{{$accountFlow->isNegative() ? '-' : '+'}}{{$forecastValue}}</b>
                                            {{__('on')}}
                                            <b>{{Carbon\Carbon::parse($forecastDate)->format('Y-m-d, l')}}</b>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <x-ui.error>{{__('Forecast is empty. This rule will not affect the data')}}</x-ui.error>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="table w-full mt-4">
                    <div class="table-row">
                        <div class="table-cell w-full pb-4 text-right">
                            <x-ui.button-secondary class="mr-4 uppercase" wire:click="$emit('closeModal')" type="button">
                                {{__('Cancel')}}
                            </x-ui.button-secondary>

                            <x-ui.button-normal wire:loading.attr="disabled" class="uppercase" type="button">
                                {{ __('Fill') }}
                            </x-ui.button-normal>
                        </div>
                    </div>
                </div>

            </form>
    </div>

    @elseif($tab2)

    <div id="active_tab2">
    <form wire:submit.prevent="onestore">
                <div class="table w-full mt-10">

                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Value') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4">
                            <div class="flex">
                                <div class="w-1/12 text-3xl">
                                    {{$accountFlow->isNegative() ? '-' : '+'}}
                                </div>
                                <div class="w-11/12">
                                    <x-jet-input
                                        id="title"
                                        class="w-full"
                                        type="text"
                                        name="value"
                                        autocomplete="off"
                                        wire:model.lazy="value"
                                    />
                                </div>
                            </div>
                            <x-jet-input-error for="value" class="mt-2 text-left"/>
                        </div>
                    </div>

                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Date start') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4">
                            <x-jet-input
                                id="oneday"
                                class="w-full"
                                type="date"
                                name="oneday"
                                wire:model.lazy="oneday"
                            />
                            <x-jet-input-error for="oneday" class="mt-2 text-left"/>
                        </div>
                    </div>
                    
                </div>
                <div class="table w-full mt-4">
                    <div class="table-row">
                        <div class="table-cell w-full pb-4 text-right">
                            <x-ui.button-secondary class="mr-4 uppercase" wire:click="$emit('closeModal')" type="button">
                                {{__('Cancel')}}
                            </x-ui.button-secondary>

                            <x-ui.button-normal wire:loading.attr="disabled" class="uppercase" type="button">
                                {{ __('Fill') }}
                            </x-ui.button-normal>
                        </div>
                    </div>
                </div>

    </form>     
</div>
@endif 

@else
            {{__('Access denied')}}
        @endif

</div>


<!-- <script>
    $(document).ready(function(){
        $('#tab1 a').addClass('border-b-2 border-indigo-400');
        $('#active_tab2').hide();
        $('#tab1').click(function(){
            $('#active_tab1').show();
            $('#tab1 a').addClass('border-b-2 border-indigo-400');
            $('#active_tab2').hide();
            $('#tab2 a').removeClass('border-b-2 border-indigo-400');
        })
        $('#tab2').click(function(){
            $('#active_tab2').show();
            $('#tab2 a').addClass('border-b-2 border-indigo-400');
            $('#active_tab1').hide();
            $('#tab1 a').removeClass('border-b-2 border-indigo-400');
        })
       
    })
</script> -->

