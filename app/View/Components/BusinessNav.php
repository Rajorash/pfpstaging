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
//        $this->links = $this->buildLinks($businessId);
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
//
//    public function buildLinks($businessId)
//    {
//        $links = collect([
//            "/business/${businessId}/accounts" => [
//                'title' => "Accounts",
//                'iconView' => 'icons.vallet',
//                'active' => (
//                        request()->is('*business/*')
//                        || request()->is('business')
//                    )
//                    && !request()->routeIs('allocations-calendar')
//            ],
//            route('allocations-calendar', ['business' => $this->business]) => [
//                'title' => "Allocations",
//                'iconView' => 'icons.calculator',
//                'active' => request()->routeIs('allocations-calendar')
//            ],
//            route('allocations-percentages', ['business' => $this->business]) => [
//                'title' => "Percentages",
//                'iconView' => 'icons.pie',
//                'active' => request()->routeIs('allocations-percentages')
//            ],
//            route('projections', ['business' => $this->business]) => [
//                'title' => "Projections",
//                'iconView' => 'icons.presentation-chart',
//                'active' => request()->routeIs('projections')
//            ],
//        ]);
//
//        return $links;
//    }
}
