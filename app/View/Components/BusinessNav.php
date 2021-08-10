<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BusinessNav extends Component
{
    public $businessId;
    public $links;
    public $business;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($businessId, $business)
    {
        $this->businessId = $businessId;
        $this->business = $business;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.business-nav');
    }

}
