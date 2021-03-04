<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Allocation;
use App\Models\BankAccount;
use Carbon\Carbon;
use Livewire\Component;

class AccountValue extends Component
{

    public $accountId;
    public BankAccount $account;
    public $allocation;
    public $amount;
    public $date;
    public $phase_id = 1;

    protected $listeners = ['updateAccountValue'];
    //
    public function mount($accountId, $date) {

        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        $this->allocation = self::getAllocation($date);
        $this->amount = $this->allocation
            ? number_format($this->allocation->amount, 0, '.', '')
            : 0;

    }

    /**
     * get the allocation for the account
     *
     * @param [type] $accountId
     * @param [type] $date
     * @return Allocation|null
     */
    private function getAllocation($date) {

        $allocation = $this->account->getAllocationByDate($date);

        return $allocation ?? null;
    }

    /**
     * The livewire component render method
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.calculator.account-value');
    }

    /**
     * Validate and store the Allocation
     *
     * @return void
     */
    public function store() {
        $this->validate([
            'amount' => 'numeric|nullable'
        ]);

        $data = array(
            'amount' => $this->amount
        );

        $values = [
            $this->amount,
            $this->date,
            $this->phase_id
        ];

        $this->account->allocate(...$values);

    }

    public function updateAccountValue(array $params)
    {
        if ($params['account_id'] == $this->accountId) {
            $negative = $this->account->flows->pluck('negative_flow', 'id')[$params['flow_id']];
            if (Carbon::parse($params['date_str']) == $this->date) {
                $this->amount = $params['amount'];
                if ($negative) {
                    $this->amount *= -1;
                }
            }
            $previousDate = clone $this->date;
            $previousAllocation = self::getAllocation($previousDate->subDays(1));
            if($previousAllocation) {
                $flowAmount = self::getFlowAllocation($params['flow_id'], $this->date->toDateString());
                $this->amount = $previousAllocation->amount +
                    ($flowAmount
                        ? ($negative ? $flowAmount->amount * -1 : $flowAmount->amount)
                        : 0);
            }

            $this->store();
            return $this->render();
        }
    }

    private function getFlowAllocation($flowId, $date) {
        $allocation = Allocation::where([
            ['allocatable_type', '=', 'App\Models\AccountFlow'],
            ['allocatable_id', '=', $flowId],
            ['allocation_date', '=', $date]
        ])->first();


        return $allocation ?? null;
    }

    /**
     * Functions carried out once $amount has finished updating
     *
     * @return void
     */
    public function updatedAmount() {
//        $this->store();
        $this->emit('updateAccountTotal');
    }

}
