<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Allocation;
use App\Models\BankAccount;
use Livewire\Component;
use Carbon\Carbon;

class AccountTotal extends Component
{
    public $accountId;
    public BankAccount $account;
    public $allocation;
    public $amount;
    public $date;
    public $phase_id = 1;

    protected $listeners = ['updateRevenueAccountTotal', 'updatePretotalAccountTotal'];

    public function mount($accountId, $date) {

        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        if ($this->account->type == 'revenue') {
            $this->amount = self:: getTotal();
        } else {
            $this->allocation = self::getAllocation($date);
            $this->amount = $this->allocation
                ? number_format($this->allocation->amount, 0, '.', '')
                : 0;
        }

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
     * Update the total if a flow value has changed for the given date
     *
     * @param  array  $params
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function updateRevenueAccountTotal(array $params)
    {
        if ($params['account_id'] == $this->accountId && Carbon::parse($params['date_str']) == $this->date) {
            $newAmount = self:: getTotal();
            if ($this->amount != $newAmount) {
                $this->amount = $newAmount;
                return $this->render();
            }
        }
    }

    public function updatePretotalAccountTotal(array $params)
    {
        if ($params['account_id'] == $this->accountId && Carbon::parse($params['date_str']) == $this->date) {
            $this->amount = $params['amount'];
            if ($this->account->flows->pluck('negative_flow', 'id')[$params['flow_id']]) {
                $this->amount *= -1;
            }
//            $this->store();
            $this->emit('updateAccountValue', $params);
            return $this->render();
        }
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

    public function render()
    {
        return view('livewire.calculator.account-total');
    }

    /**
     * Get the sum of allocations for the given date and current account
     *
     * @return mixed
     */
    private function getTotal()
    {
        return $this->account->getAllocationsTotalByDate($this->date);
    }
}
