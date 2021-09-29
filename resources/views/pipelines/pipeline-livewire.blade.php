<div class="livewire-wrapper">
    @if(Auth::user()->isAdvisor() || Auth::user()->isClient())

        <form wire:submit.prevent="store">
            <div class="table w-full mt-10">
                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Name of pipeline') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input
                            id="title"
                            class="w-full"
                            type="text"
                            name="title"
                            wire:model.lazy="title"
                        />
                        <x-jet-input-error for="title" class="mt-2 text-left"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Tags') }}
                        <span class="text-xs">{{__('separate by coma')}}</span>
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input
                            id="tags"
                            class="w-full"
                            type="text"
                            name="tags"
                            wire:model.lazy="tags"
                        />
                        <x-jet-input-error for="tags" class="mt-2 text-left"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left align-top">
                        {{ __('Notes') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-forms.textarea
                            id="notes"
                            class="w-full"
                            name="notes"
                            wire:model.lazy="notes"
                        ></x-forms.textarea>
                        <x-jet-input-error for="notes" class="mt-2 text-left"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Certainty') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input
                            id="certainty"
                            class="w-full"
                            type="number" min="1" max="100" step="1"
                            name="certainty"
                            wire:model.lazy="certainty"
                        />
                        <x-jet-input-error for="certainty" class="mt-2 text-left"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Recurring') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left inline-flex">
                        <x-jet-input
                            id="recurring"
                            type="checkbox"
                            name="recurring"
                            class="inline-block align-middle"
                            wire:model.lazy="recurring"
                        />
                        <x-jet-label for="recurring"
                                     class="ml-2 inline-block align-middle">{{__('Set Pipeline as Recurring')}}</x-jet-label>
                        <x-jet-input-error for="recurring" class="mt-2 text-left"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Value') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <div class="flex">
                            <div class="w-1/12 text-3xl">
                                +
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
                            wire:model.lazy="date_start"
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

                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        {{$description}}
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{__('Forecast')}}
                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        @if (!empty($forecast))
                            @foreach ($forecast as $forecastDate => $forecastValue)
                                <div>
                                    <b>+{{$forecastValue}}</b>
                                    {{__('on')}}
                                    <b>{{Carbon\Carbon::parse($forecastDate)->format('Y-m-d, l')}}</b>
                                </div>
                            @endforeach
                        @else
                            <x-ui.error>{{__('Forecast is empty. This rule will not affect the data')}}</x-ui.error>
                        @endif
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">

                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        <x-ui.button-primary
                            class=""
                            wire:click="store"
                            wire:loading.attr="disabled">
                            {{ __('Save') }}
                        </x-ui.button-primary>
                    </div>
                </div>
            </div>
        </form>

    @else
        {{__('Access denied')}}
    @endif
</div>
