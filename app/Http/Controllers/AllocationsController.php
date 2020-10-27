<?php

namespace App\Http\Controllers;

use Auth;
use App\AccountFlow;
use App\BankAccount;
use App\Business;
use Carbon\Carbon as Carbon;
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
        $this->authorize('view', $business);
        $today = Carbon::now();
        $start_date = Carbon::now()->addDays(-15);
        $end_date = Carbon::now()->addDays(15);

        return view('allocations.calculator', compact(['business', 'today', 'start_date', 'end_date']));
    }

    /**
     * Show the percentages for the selected business
     *
     * @return \Illuminate\Http\Response
     */
    public function percentages(Business $business)
    {
        $this->authorize('view', $business);
        $rollout = $business->rollout->orderBy('end_date');

        return view('allocations.percentages', compact('business', 'rollout'));
    }

    // Used to update or create allocations 
    public function update(Request $request) {
        
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required',
            'amount' => 'required|integer'
        ]);
        
        
        return response()->JSON([
            "msg" => "allocation successfully updated."
        ]);
    }


}
