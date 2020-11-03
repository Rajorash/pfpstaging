<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\AccountFlow;
use App\Allocation;
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
        $start_date = Carbon::now()->addDays(-6);
        $end_date = Carbon::now()->addDays(7);

        $dates = array();
        for($date = $start_date; $date <= $end_date; $date->addDay(1))
        {
            $dates[] = $date->format('Y-m-j');
        }

        $allocations = $business->allocations()->sortBy('allocation_date');

        return view('allocations.calculator', compact(['business', 'today', 'start_date', 'end_date', 'dates', 'allocations']));
    }

    /**
     * Show the percentages for the selected business
     *
     * @return \Illuminate\Http\Response
     */
    public function percentages(Business $business)
    {
        $this->authorize('view', $business);
        $rollout = $business->rollout->sortBy('end_date');

        return view('allocations.percentages', compact('business', 'rollout'));
    }

    // Used to update or create allocations
    public function update(Request $request) {

        $valid = $request->validate([
            'id' => 'required|integer',
            'allocation_type' => 'required',
            'allocation_date' => 'required',
            'amount' => 'required|integer'
        ]);

        // find allocation matching type and id
        $allocations = Allocation::where('allocatable_id', '=', $valid['id'])->where('allocatable_type', 'like', "%".$valid['allocation_type'] )->where('allocation_date', '=', $valid['allocation_date'] )->get();


        // if there is no existing allocation, insert one.
        if( $allocations->count() < 1 ) {

            $new_allocation = new Allocation();

            $new_allocation->phase_id = 1;
            $new_allocation->allocatable_id = $valid['id'];
            $new_allocation->allocatable_type = "App" . "\\" . $valid['allocation_type'];
            $new_allocation->amount = $valid['amount'];
            $new_allocation->allocation_date = $valid['allocation_date'];

            if ( !$new_allocation->save() ) {
                return response(["msg" => "allocation not created"], 400);
            }

            return response()->JSON([
                "msg" => "created new allocation",
                "new allocation" => $new_allocation
            ]);

        }

        // removed auth check to finish writing logic
        // $this->authorize('view', $allocation->phase->business);

        $allocation = $allocations->first();
        $allocation->amount = $valid['amount'];
        $allocation->save();

        return response()->JSON([
            "msg" => "allocation successfully updated.",
            "allocation" => $allocation
        ]);
    }

}
