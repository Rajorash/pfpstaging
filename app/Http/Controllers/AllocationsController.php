<?php

namespace App\Http\Controllers;

use Auth;
use App\AccountFlow;
use App\BankAccount;
use App\Business;
use Illuminate\Http\Request;

class AllocationsController extends Controller
{
    /**
     * Display a listing of the businesses the authorised user can see to select from
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $businesses = Business::all();

        $filtered = $businesses->filter( function ($business) {
            return Auth::user()->can('view', $business);
        })->values();
        
        return view('business.list', ['businesses' => $filtered]);
    }

    
    /**
     * Show the allocations for the selected business
     *
     * @return \Illuminate\Http\Response
     */
    public function allocations(Business $business)
    {
        $accounts = [
            ['label' => 'Profit', 'percentage' => 35],
            ['label' => 'Opex', 'percentage' => 50],
            ['label' => 'General', 'percentage' => 15]
        ];
        return view('allocations.calculator', ['accounts' => $accounts]);
    }

    /**
     * Show the percentages for the selected business
     *
     * @return \Illuminate\Http\Response
     */
    public function percentages(Business $business)
    {
        $accounts = [
            ['label' => 'Profit', 'percentage' => 35],
            ['label' => 'Opex', 'percentage' => 50],
            ['label' => 'General', 'percentage' => 15]
        ];
        return view('allocations.percentages', ['accounts' => $accounts]);
    }


}
