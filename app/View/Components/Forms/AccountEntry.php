<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;
use App\Models\Business;


class AccountEntry extends Component
{

    public $business;
    // public $accounts;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Business $business)
    {
        $this->business = $business;
        // $this->accounts = $business->accounts;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.forms.account-entry');
    }
}
