<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Traits\GettersTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AllocationCalculatorController extends Controller
{
    use GettersTrait;

    /**
     * render the view of the allocation calculator
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getView();
    }

    public function indexWithId(Business $business)
    {
        return $this->getView($business);
    }

    public function getView(Business $business = null)
    {
        $businesses = $this->getBusinessAll();

        $filtered = $businesses->filter(function ($business) {
            return Auth::user()->can('view', $business);
        })->values();

        return view('calculator.allocation-calculator', [
            'businesses' => $filtered,
            'business' => $business,
            'businessId' => optional($business)->id,
        ]);
    }
}
