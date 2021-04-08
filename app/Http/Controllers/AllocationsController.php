<?php

namespace App\Http\Controllers;

use App\Models\AccountFlow;
use App\Models\Allocation;
use App\Models\AllocationPercentage;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\Phase;
use App\Traits\GettersTrait;
use Carbon\Carbon as Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AllocationsController extends Controller
{
    use GettersTrait;

    /**
     * Display a listing of the businesses the authorised user can see to select from
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $businesses = $this->getBusinessAll();

        $filtered = $businesses->filter(function ($business) {
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
        for ($date = $start_date; $date <= $end_date; $date->addDay(1)) {
            $dates[] = $date->format('Y-m-j');
        }

//        $allocations = $business->allocations()->sortBy('allocation_date');

        $businessObj = $this->getBusinessById($business->id);
        $allocations = $businessObj->allocations()->sortBy('allocation_date');
        $allocatables = array();
        // $taxRates = array();

        foreach ($businessObj->accounts as $account) {
            $allocatables[] = ['label' => $account->name, 'type' => 'BankAccount', 'id' => $account->id];

            // tax rates are the same as allocation percentages
            // if ($account->taxRate)
            // {
            //     $taxRates[$account->id] = $account->taxRate->rate;
            // }

            foreach ($account->flows as $flow) {
                $allocatables[] = ['label' => $flow->label, 'type' => 'AccountFlow', 'id' => $flow->id];
            }
        }

        $allocationPercentages = self::buildAllocationPercentages($businessObj);
        $phaseDates = self::buildPhaseDates($dates, $business);
        $allocationValues = self::buildAllocationValues($dates, $allocatables);

        // testing original view and livewire rebuild, comment out the one you don't want. WIP live wire version has some bugs I cannot diagnose, may be easier to remove and start again.
        // $view = 'allocations.calculator';
        $view = 'allocations.calculator-lw';

        $data = [
            'business', 'today', 'start_date', 'end_date', 'dates', 'allocations', 'allocatables', 'allocationValues',
            'allocationPercentages', 'phaseDates'
        ];

        return view($view, compact($data));

    }

    public static function buildPhaseDates(array $dates, Business $business)
    {
        $phaseDates = array();

//        $phases = Phase::where('business_id', '=', $business->id)->orderBy('end_date')->get();
        $key = 'getPhaseById_'.$business->id;
        $phases = Cache::get($key);

        if ($phases === null) {
            $phases = Phase::where('business_id', '=', $business->id)->orderBy('end_date')->get();
            Cache::put($key, $phases);
        }

        $currentEndDate = 0;
        foreach ($phases as $phase) {
            foreach ($dates as $date) {
                if (Carbon::parse($date) <= Carbon::parse($phase->end_date) && Carbon::parse($date) > Carbon::parse($currentEndDate)) {
                    $phaseDates[$date] = $phase->id;
                }
            }
            $currentEndDate = $phase->end_date;
        }

        return $phaseDates;
    }

    public static function buildAllocationPercentages(Business $business)
    {
        $allocationPercentages = [];

        foreach ($business->accounts as $account) {

            $percentageCollection = $account->getAllocationPercentages();
            foreach ($percentageCollection as $allocation_percentage) {
                $phase_id = $allocation_percentage->phase_id;

                $allocationPercentages[$phase_id][$account->id] = $allocation_percentage->percent ?? 0;
            }

        }

        return $allocationPercentages;
    }

    /**
     * Used to update or create allocations
     */
    public function updateAllocation(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required|integer',
            'allocation_type' => 'required',
            'allocation_date' => 'required',
            'amount' => 'present|numeric|nullable'
        ]);

        // find allocation matching type and id
        $allocations = Allocation::where('allocatable_id', '=', $valid['id'])
            ->where('allocatable_type', 'like', "%".$valid['allocation_type'])
            ->where('allocation_date', '=', $valid['allocation_date'])
            ->get();

        // if there is no existing allocation, insert one.
        if ($allocations->count() < 1) {

            $new_allocation = new Allocation();

            $new_allocation->phase_id = 1;
            $new_allocation->allocatable_id = $valid['id'];
            $new_allocation->allocatable_type = "App"."\\Models\\".$valid['allocation_type'];
            $new_allocation->amount = $valid['amount'];
            $new_allocation->allocation_date = $valid['allocation_date'];

            if (!$new_allocation->save()) {
                return response(["msg" => "allocation not created"], 400);
            }

            return response()->JSON([
                "msg" => "created new allocation",
                "new allocation" => $new_allocation
            ]);

        }

        $allocation = $allocations->first();
        // if amount is empty remove the allocation -- please note that 0 is a valid amount
        if (!$valid['amount']) {
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

    public static function buildAllocationValues(array $dates, array $allocatables)
    {
        $allocationValues = [];

//        foreach($allocatables as $allocatable)
//        {
//            foreach($dates as $date)
//            {
//                $allocation = Allocation::where('allocation_date', '=', $date)
//                    ->where('allocatable_id', '=', $allocatable['id'])
//                    ->where('allocatable_type', 'like', '%'.$allocatable['type'])
//                    ->get();
//
//                if($allocation->count())
//                {
//                    $allocationValues[$allocatable['type']][$allocatable['id']][$date] = (int)$allocation[0]->amount;
//                }
//
//            }
//        }

        $searchArray = [];
        foreach ($allocatables as $allocatable) {
            $searchArray[$allocatable['type']][] = $allocatable['id'];
        }

        foreach ($searchArray as $type => $idsArray) {

            $key = 'Allocation_'.implode('|', $dates).'_'.implode('|', $idsArray).'_'.$type;

            $allocation = Cache::get($key);

            if ($allocation === null) {
                $allocation = Allocation::whereIn('allocation_date', $dates)
                    ->whereIn('allocatable_id', $idsArray)
                    ->where('allocatable_type', 'like', '%'.$type)
                    ->get()->toArray();
                Cache::put($key, $allocation);
            }
//
//            $allocation = Allocation::whereIn('allocation_date', $dates)
//                ->whereIn('allocatable_id', $idsArray)
//                ->where('allocatable_type', 'like', '%'.$type)
//                ->get()->toArray();

            if (count($allocation)) {
                foreach ($allocation as $row) {
                    $allocationValues[$type][$row['allocatable_id']][$row['allocation_date']] = (int) $row['amount'];
                }
//                ksort($allocationValues[$type][$row['allocatable_id']], SORT_NATURAL);
            }
//            ksort($allocationValues[$type], SORT_NATURAL);
        }
//        ksort($allocationValues, SORT_NATURAL);

        return $allocationValues;
    }


    /**
     * Show the percentages for the selected business
     *
     * @return \Illuminate\Http\Response
     */
    public function percentages(Business $business)
    {
        $this->authorize('view', $business);

        return view('business.percentages', ['business' => $business]);
    }

    public function updatePercentages(Request $request)
    {
        $response = [
            'error' => [],
            'html' => [],
        ];

        $businessId = $request->businessId;

        if (isset($request->cells) && count($request->cells) > 0) {
            foreach ($request->cells as $cell) {
                AllocationPercentage::updateOrCreate([
                    'phase_id' => $cell['phaseId'],
                    'bank_account_id' => $cell['accountId']
                ], [
                    'phase_id' => $cell['phaseId'],
                    'bank_account_id' => $cell['accountId'],
                    'percent' => $cell['cellValue']
                ]);
                $key = 'phasePercentValues_'.$cell['phaseId'].'_'.$businessId;
                Cache::forget($key);
            }
        }

        if (!$businessId) {
            $response['error'][] = 'Business ID not set';
        } else {
        }

        $business = Business::find($businessId);

        $percentages = self::buildPercentageValues($business);

        $totals = $percentages->groupBy('phase_id')
            ->map(function ($item){
                return $item->sum('percent');
            })->toArray();

        $rollout = $business->rollout
            ->sortBy('end_date')
            ->map(function($item) use ($totals) {
                $item['total'] = $totals[$item->id];
                return $item;
            });

        $response['html'] = view('business.percentages-table')
            ->with([
                'percentages' => $percentages,
                'rollout' => $rollout,
                'business' => $business
            ])->render();

        return response()->json($response);
    }

    /**
     * Used to update or create allocations
     */
    public function updatePercentage(Request $request)
    {
        $valid = $request->validate([
            'phase_id' => 'required|numeric',
            'bank_account_id' => 'required|numeric',
            'percent' => 'present|numeric|min:0|max:100|nullable'
        ]);

        // find allocation matching type and id
        $percentages = AllocationPercentage::where('phase_id', '=', $valid['phase_id'])
            ->where('bank_account_id', '=',
                $valid['bank_account_id'])
            ->get();

        // if there is no existing allocation, insert one.
        if ($percentages->isEmpty()) {

            $new_percentage = new AllocationPercentage();

            $new_percentage->phase_id = $valid['phase_id'];
            $new_percentage->bank_account_id = $valid['bank_account_id'];
            $new_percentage->percent = $valid['percent'];

            if (!$new_percentage->save()) {
                return response(["msg" => "percentage not created"], 400);
            }

            return response()->JSON([
                "msg" => "created new percentage value"
            ]);
        }

        // return response()->JSON($percentages);
        $percentage = $percentages->first();

        // if percent is empty remove the percentage -- please note that 0 is a valid percent
        if (!$valid['percent']) {
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

    public static function buildPercentageValues(Business $business)
    {
        $phase_ids = $business->rollout->pluck('id');

        $percentages = AllocationPercentage::whereIn('phase_id', $phase_ids)->get([
            'phase_id', 'bank_account_id', 'percent'
        ]);

//         $percentageValues = $percentages->groupBy(['bank_account_id', 'phase_id'])->toArray();
        // foreach($percentages as $entry)
        // {
        //     $percentageValues[$entry->bank_account_id][$entry->phase_id] = $entry->percent;
        // }

        return $percentages;
    }

    private function getBusinessById($business_id)
    {
        $key = 'getBusinessById_'.$business_id;
        $getBusinessById = Cache::get($key);

        if ($getBusinessById === null) {
            $getBusinessById = Business::find($business_id)->with(['accounts'])->first();
            Cache::put($key, $getBusinessById);
        }

        return $getBusinessById;
    }

    private function getPhaseById($business_id)
    {
        $key = 'getPhaseById_'.$business_id;
        $getPhaseById = Cache::get($key);

        if ($getPhaseById === null) {
            $getPhaseById = Phase::where('business_id', '=', $business_id)->orderBy('end_date')->get();
            Cache::put($key, $getPhaseById);
        }

        return $getPhaseById;
    }

}
