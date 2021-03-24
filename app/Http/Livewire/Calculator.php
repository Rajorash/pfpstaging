<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;

class Calculator extends Component
{
    use WithPagination;

    public $daysPerPage;
    public $startDate;
    public $dateInput;
    public $dates;

    public $business;
    public $accounts;
    public $types = ['revenue', 'pretotal', 'salestax', 'prereal', 'postreal'];

    public function mount() {
        $this->daysPerPage = 14;
    }

    public function render($startDate = null, $dateInput = null)
    {
        if (! $this->startDate ) {
            $this->startDate = Carbon::now()->today();
        } else {
            $this->startDate = Carbon::parse($this->dateInput);
        }

        $this->accounts = $this->sortAccounts();
        $this->dates = $this->getDates($startDate);

//        $this->emit('calculatorRerender', $this->dates);
        return view('components.calculator.table', ['dates' => $this->dates, 'accounts' => $this->accounts]);
    }

    /**
     * Return an array of Carbon dates to use for the view
     */

    public function getDates() {

        // fill out the the dates as per the daysPerPage
        for ($day=0; $day < $this->daysPerPage; $day++) {
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

    public function updatedDaysPerPage()
    {
        $this->render();
    }
}
