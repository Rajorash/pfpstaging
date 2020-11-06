<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\AccountFlow;
use App\Allocation;
use App\AllocationPercentage;
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

        $allocatables = array();

        foreach($business->accounts as $account)
        {
            $allocatables[] = ['label' => $account->name, 'type' => 'BankAccount', 'id' => $account->id ];
            foreach($account->flows as $flow)
            {
                $allocatables[] = ['label' => $flow->label, 'type' => 'AccountFlow', 'id' => $flow->id ];
            }
        }

        $allocationValues = self::buildAllocationValues($dates, $allocatables);

        return view('allocations.calculator', compact(['business', 'today', 'start_date', 'end_date', 'dates', 'allocations', 'allocatables', 'allocationValues']));
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

    /**
     * Used to update or create allocations
     */
    public function updateAllocation(Request $request) {

        $valid = $request->validate([
            'id' => 'required|integer',
            'allocation_type' => 'required',
            'allocation_date' => 'required',
            'amount' => 'present|numeric|nullable'
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

        $allocation = $allocations->first();
        // if amount is empty remove the allocation -- please note that 0 is a valid amount
        if (!$valid['amount'])
        {
            $allocation->delete();
            return response()->JSON([
                "msg" => "allocation successfully deleted."
            ]);
        }
        // removed auth check to finish writing logic
        // $this->authorize('view', $allocation->phase->business);


        // otherwise if allocation exists and amount is valid, update the allocation
        $allocation->amount = $valid['amount'];
        $allocation->save();

        return response()->JSON([
            "msg" => "allocation successfully updated.",
            "allocation" => $allocation
        ]);
    }

    public static function buildAllocationValues(Array $dates, Array $allocatables)
    {
        $allocationValues = [];

        foreach($allocatables as $allocatable)
        {
            foreach($dates as $date)
            {
                $allocation = Allocation::where('allocation_date', '=', $date)
                    ->where('allocatable_id', '=', $allocatable['id'])
                    ->where('allocatable_type', 'like', '%'.$allocatable['type'])
                    ->get();

                if($allocation->count())
                {
                    $allocationValues[$allocatable['type']][$allocatable['id']][$date] = number_format($allocation[0]->amount, 0);
                    // array( 'allocation' => $allocation, "T:" . $allocatable['type'] . "-I:" . $allocatable['id'] . "-D:" . $date);
                }

            }
        }

        return $allocationValues;

    }

        /**
     * Used to update or create allocations
     */
    public function updatePercentage(Request $request) {

        $valid = $request->validate([
            'phase_id' => 'required|numeric',
            'bank_account_id' => 'required|numeric',
            'percent' => 'present|integer|min:0|max:100|nullable'
        ]);

        // find allocation matching type and id
        $percentages = AllocationPercentage::where('phase_id', '=', $valid['phase_id'])->where('bank_account_id', '=', $valid['bank_account_id'] )->get();

        // if there is no existing allocation, insert one.
        if( $percentages->isEmpty() ) {

            $new_percentage = new AllocationPercentage();

            $new_percentage->phase_id = $valid['phase_id'];
            $new_percentage->bank_account_id = $valid['bank_account_id'];
            $new_percentage->percent = $valid['percent'];

            if ( !$new_percentage->save() ) {
                return response(["msg" => "percentage not created"], 400);
            }

            return response()->JSON([
                "msg" => "created new percentage value"
            ]);

        }

        // return response()->JSON($percentages);
        $percentage = $percentages->first();

        // if percent is empty remove the percentage -- please note that 0 is a valid percent
        if (!$valid['percent'])
        {
            $percentage->delete();
            return response()->JSON([
               "msg" => "percentage successfully deleted."
            ]);
        }
        // removed auth check to finish writing logic
        // $this->authorize('view', $percentage->phase->business);

        // otherwise if percentage exists and percent is valid, update the percentage
        $percentage->percent = $valid['percent'];
        $percentage->save();

        return response()->JSON([
            "msg" => "percentage successfully updated."
        ]);
    }


}
