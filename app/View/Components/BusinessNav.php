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
        $this->links = $this->buildLinks($businessId);
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

    public function buildLinks($businessId)
    {
        $links = collect([
            "/business/${businessId}/accounts" => "Accounts",
//            "/allocations/${businessId}" => "Allocations",
            route('allocations-calendar', ['business' => $this->business]) => "Allocations",
//            "/allocations/${businessId}/percentages" => "Percentages",
            route('allocations-percentages', ['business' => $this->business]) => "Percentages",
//            "/projections/${businessId}" => "Projections",
            route('projections', ['business' => $this->business]) => "Projections",
        ]);

        return $links;
    }
}
