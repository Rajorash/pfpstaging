<?php

namespace App\Http\Livewire\Calculator;

use App\Models\BankAccount;
use Livewire\Component;
use Carbon\Carbon;

class AccountTotal extends Component
{
    public $accountId;
    public BankAccount $account;
    public $amount;
    public $date;

    protected $listeners = ['updateRevenueAccountTotal', 'updatePretotalAccountTotal'];

    public function mount($accountId, $date) {

        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        $this->amount = self:: getTotal();
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
            $this->emit('updateAccountValue', $params);
        }
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
