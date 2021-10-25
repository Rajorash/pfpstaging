<?php

namespace App\Http\Livewire;

use App\Http\Controllers\RecurringTransactionsController;
use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\RecurringTransactions;
use App\Traits\GettersTrait;
use App\Traits\TablesTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Livewire\Component;
use Str;

class QuickEntryDataForFlow extends Component
{
    use GettersTrait, TablesTrait;

    public AccountFlow $accountFlow;
    public BankAccount $bankAccount;
    protected Business $business;
    public array $forecast = [];

    protected RecurringTransactionsController $RecurringTransactionsController;

    public array $repeatTimeArray = [];
    public array $weekDaysArray = [];

    public float $value = 0.0;
    public string $date_start = '';
    public string $date_end = '';
    public int $repeat_every_number = 1;
    public string $repeat_every_type = '';
    public array $repeat_rules_week_days = [];

    public int $flowId = 0;
    public int $accountId = 0;

    /**
     * @param  null  $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->RecurringTransactionsController = new RecurringTransactionsController();
    }

    /**
     * @param  int  $accountId
     * @param  int  $flowId
     */
    public function mount(int $accountId, int $flowId = 0)
    {
        $this->accountId = $accountId;
        $this->flowId = $flowId;

        $this->bankAccount = BankAccount::findOrFail($this->accountId);
        $this->accountFlow = AccountFlow::findOrFail($this->flowId);

        $this->repeatTimeArray = RecurringTransactions::getRepeatTimeArray();
        $this->weekDaysArray = RecurringTransactions::getWeekDaysArray();

        $this->value = 0;
        $this->date_start = Timezone::convertToLocal(Carbon::now(), 'Y-m-d');
        $this->date_end = Timezone::convertToLocal(Carbon::now()->addMonths(3), 'Y-m-d');
        $this->repeat_every_number = 1;
        $this->repeat_every_type = RecurringTransactions::REPEAT_DEFAULT;
        $this->repeat_rules_week_days = [
            strtolower(Carbon::now()->format('l'))
        ];

        $recurring = $this->_updateRecurringObject();
        $this->forecast = $this->RecurringTransactionsController->getForecast($recurring);
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'value' => 'required|numeric|gt:0',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after:date_start',
            'repeat_rules_week_days' => 'required_if:repeat_every_type,'.RecurringTransactions::REPEAT_WEEK
        ];
    }

    /**
     *
     */
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

        $recurring = $this->_updateRecurringObject();
        $this->forecast = $this->RecurringTransactionsController->getForecast($recurring);
    }

    /**
     * @return RecurringTransactions
     */
    private function _updateRecurringObject(): RecurringTransactions
    {
        $recurring = new RecurringTransactions();

        $recurring->value = $this->value;

        $recurring->date_start = $this->date_start;
        $recurring->date_end = $this->date_end ?? null;

        $recurring->repeat_every_number = $this->repeat_every_number;
        $recurring->repeat_every_type = $this->repeat_every_type;
        if ($this->repeat_every_type == RecurringTransactions::REPEAT_WEEK) {
            $recurring->repeat_rules = [
                'days' => $this->repeat_rules_week_days
            ];
        }

        return $recurring;
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function store()
    {
        $this->validate();

        $recurring = $this->_updateRecurringObject();

        $this->forecast = $this->RecurringTransactionsController->getForecast($recurring);

        $this->business = Business::findOrFail($this->bankAccount->business->id);

        foreach ($this->forecast as $date => $float) {
            $this->storeSingle(
                'flow',
                $this->flowId,
                $float,
                $date,
                false,
                false
            );
        }

        return redirect("business/".$this->bankAccount->business->id."/revenue-entry");
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        return view('business.quick-entry-data-for-flow');
    }
}
