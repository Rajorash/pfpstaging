<?php

namespace App\Http\Livewire\Calculator;

use App\Models\BankAccount;
use Livewire\Component;

class AccountRow extends Component
{
    public BankAccount $acc;
    public $dates;
    public $rowId;
    public $first;
    public $type;

    public function render()
    {
        return view('livewire.calculator.account-row');
    }
}
