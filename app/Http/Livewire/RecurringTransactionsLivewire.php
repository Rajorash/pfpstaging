<?php

namespace App\Http\Livewire;

use App\Http\Controllers\RecurringTransactionsController;
use App\Models\RecurringTransactions;
use Carbon\Carbon;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Livewire\Component;
use Str;

class RecurringTransactionsLivewire extends Component
{
    public $accountFlow;
    public $bankAccount;
    public $recurringTransactions;
    public $forecast;

    protected $RecurringTransactionsController;

    public $repeatTimeArray;
    public $weekDaysArray;

    public $description;

    public $title;
    public $value;
    public $date_start;
    public $date_end;
    public $repeat_every_number;
    public $repeat_every_type;
    public $repeat_rules_week_days = [];

    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->RecurringTransactionsController = new RecurringTransactionsController();
    }

    public function mount()
    {
        $this->repeatTimeArray = RecurringTransactions::getRepeatTimeArray();
        $this->weekDaysArray = RecurringTransactions::getWeekDaysArray();

        if (is_object($this->recurringTransactions)
            && is_a($this->recurringTransactions, 'App\Models\RecurringTransactions')) {
            $this->title = $this->recurringTransactions->title;
            $this->description = $this->recurringTransactions->description;
            $this->value = $this->recurringTransactions->value;
            $this->date_start = Carbon::parse($this->recurringTransactions->date_start)->format('Y-m-d');
            $this->date_end = $this->recurringTransactions->date_end
                ? Carbon::parse($this->recurringTransactions->date_end)->format('Y-m-d') :
                null;

            $this->repeat_every_number = $this->recurringTransactions->repeat_every_number;
            $this->repeat_every_type = $this->recurringTransactions->repeat_every_type;
            if ($this->recurringTransactions->repeat_every_type == RecurringTransactions::REPEAT_WEEK) {
                if ($this->recurringTransactions->repeat_rules['days']) {
                    $this->repeat_rules_week_days = $this->recurringTransactions->repeat_rules['days'];
                }
            }

        } else {
            $this->value = 0;
            $this->date_start = $today = Timezone::convertToLocal(Carbon::now(),'Y-m-d');
            $this->repeat_every_number = 1;
            $this->repeat_every_type = RecurringTransactions::REPEAT_DEFAULT;
            $this->repeat_rules_week_days = [
                strtolower(Carbon::parse($today)->format('l'))
            ];
        }

        $recurring = $this->_updateRecurringobject();
        $this->forecast = $this->RecurringTransactionsController->getForecast($recurring);
    }

    public function render()
    {
        return view('recurring.recurring-transactions-livewire');
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'value' => 'required|numeric|gt:0',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after:date_start',
            'repeat_rules_week_days' => 'required_if:repeat_every_type,'.RecurringTransactions::REPEAT_WEEK
        ];
    }

    public function updated()
    {
        $this->validate();

        if ($this->value > 0) {
            $this->description = $this->accountFlow->isNegative()
                ? __('Subtraction')
                : __('Addition');

            $this->description .= ' $'.$this->value.' ';

            switch ($this->repeat_every_type) {
                case RecurringTransactions::REPEAT_DAY:
                    $this->description .=
                        __('every')
                        .' '
                        .(
                        $this->repeat_every_number > 1
                            ? $this->repeat_every_number
                            : ''
                        )
                        .' '
                        .Str::plural('day', $this->repeat_every_number);
                    break;
                case RecurringTransactions::REPEAT_WEEK:
                    if ($this->repeat_rules_week_days) {
                        $this->description .=
                            (
                            $this->repeat_every_number == 1
                                ? __('weekly')
                                : __('every')
                                .' '
                                .$this->repeat_every_number
                                .' '
                                .__('weeks')
                            )
                            .' '
                            .__('on')
                            .' '
                            .implode(', ',
                                array_map(function ($x) {
                                    return $this->weekDaysArray[$x];
                                }, $this->repeat_rules_week_days)
                            );
                    }
                    break;
                case RecurringTransactions::REPEAT_MONTH:
                    $this->description .=
                        (
                        $this->repeat_every_number == 1 ?
                            __('monthly on day')
                            : __('every')
                            .' '
                            .$this->repeat_every_number
                            .' '
                            .__('months')
                        )
                        .' '
                        .Carbon::parse($this->date_start)->format('d');
                    break;
                case RecurringTransactions::REPEAT_YEAR:
                    //Annually on August 16, until Sep 15, 2021
                    $this->description .=
                        (
                        $this->repeat_every_number == 1 ?
                            __('annually on')
                            : __('every')
                            .' '
                            .$this->repeat_every_number
                            .' '
                            .__('years')
                        )
                        .' '
                        .Carbon::parse($this->date_start)->format('F d');
                    break;
            }

            $this->description .=
                $this->date_end
                    ? ' '
                    .__('until')
                    .' '.
                    Carbon::parse($this->date_end)->format('M d, Y')
                    : '';

        } else {
            $this->description = '';
        }

        $recurring = $this->_updateRecurringobject();
        $this->forecast = $this->RecurringTransactionsController->getForecast($recurring);
    }

    private function _updateRecurringobject()
    {
        if (is_object($this->recurringTransactions)
            && is_a($this->recurringTransactions, 'App\Models\RecurringTransactions')) {
            $recurring = $this->recurringTransactions;
        } else {
            $recurring = new RecurringTransactions();
        }

        $recurring->title = $this->title;
        $recurring->description = $this->description;

        $recurring->value = $this->value;

        $recurring->date_start = $this->date_start;
        $recurring->date_end = $this->date_end ? $this->date_end : null;

        $recurring->repeat_every_number = $this->repeat_every_number;
        $recurring->repeat_every_type = $this->repeat_every_type;
        if ($this->repeat_every_type == RecurringTransactions::REPEAT_WEEK) {
            $recurring->repeat_rules = [
                'days' => $this->repeat_rules_week_days
            ];
        }

        return $recurring;
    }

    public function store()
    {
        $this->validate();

        $recurring = $this->_updateRecurringobject();

        $recurring->accountFlow()->associate($this->accountFlow);

        $recurring->save();

        $this->redirect(route('recurring-list',
            [
                'account' => $this->bankAccount,
                'flow' => $this->accountFlow
            ]
        ));
    }
}
