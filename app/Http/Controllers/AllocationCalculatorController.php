<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Traits\GettersTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AllocationCalculatorController extends Controller
{
    use GettersTrait;

    /**
     * render the view of the allocation calculator
     *
     * @return View
     */
    public function index(): View
    {
        return $this->getView();
    }

    /**
     * render the view of the allocation calculator
     *
     * @return View
     */
    public function indexWithId(Business $business): View
    {
        return $this->getView($business);
    }

    /**
     * @param  Business|null  $business
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function getView(Business $business = null): View
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
