<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BusinessNav extends Component
{
    public $businessId;
    public $links;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($businessId)
    {
        $this->businessId = $businessId;
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

    public function buildLinks($businessId) {
        $links = collect([
            "/business/${businessId}/accounts" => "Accounts",
            "/allocations/${businessId}" => "Allocations",
            "/allocations/${businessId}/percentages" => "Percentages",
            "/projections/${businessId}" => "Projections",
        ]);

        return $links;
    }
}
