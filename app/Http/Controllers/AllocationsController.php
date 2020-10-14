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
        
        return view('allocations.list', ['businesses' => $filtered]);
    }
}
