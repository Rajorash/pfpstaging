<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllocationCalculatorController extends Controller
{
    /**
     * render the view of the allocation calculator
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $businesses = Business::all();

        $filtered = $businesses->filter(function ($business) {
            return Auth::user()->can('view', $business);
        })->values();

        return view('calculator.allocation-calculator', ['businesses' => $filtered]);
    }
}
