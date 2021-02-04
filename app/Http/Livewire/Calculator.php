<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Calculator extends Component
{
    use WithPagination;

    public $daysPerPage = 14;
    public $startDate;
    public $dates;

    public $business;
    public $accounts;

    public function mount(Carbon $startDate = null) {
        // if (! $this->startDate ) {
            $this->startDate = Carbon::now()->firstOfMonth();
        // }

        $this->accounts = $this->sortAccounts();
        $this->dates = $this->getDates($startDate);

    }

    public function render()
    {
        return view('livewire.calculator');
    }

    /**
     * Return an array of Carbon dates to use for the view
     */

    public function getDates() {

        // fill out the the dates as per the daysPerPage
        for ($day=0; $day <= $this->daysPerPage; $day++) {
            $dates[] = Carbon::parse($this->startDate)->addDay($day);
        }

        return $dates;

    }

    /**
     * Return a type sorted array of accounts
     *
     * eg.[
     *      "revenue" => [ 0 => App\Models\BankAccount {#111} ],
     *      "pretotal" => [ 0 => App\Models\BankAccount {#112} ],
     *      "prereal" => [ 0 => App\Models\BankAccount {#113}, ... ],
     *      "postreal" => [ 0 => App\Models\BankAccount {#115}, ... ],
     *      "salestax" => [ 0 => App\Models\BankAccount {#134}, ... ]
     *    ]
     *
     */

    public function sortAccounts() {

        $sortedAccounts = [];

        foreach( $this->business->accounts as $acc ) {
            $sortedAccounts[$acc->type][] = $acc;
        }

        return $sortedAccounts;

    }
}
