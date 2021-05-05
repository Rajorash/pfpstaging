<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountFlow;
use App\Models\Allocation;
use App\Models\AllocationPercentage;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\Phase;
use Carbon\Carbon as Carbon;
use Illuminate\Support\Facades\Auth;

class ProjectionController extends Controller
{
    protected $defaultProjectionsRangeValue = 7;

    /**
     * Display a listing of the the accounts with projection.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        $this->authorize('view', $business);

        $rangeArray = $this->getRangeArray();
        $currentProjectionsRange = session()->get(
            'projectionRangeValue_'.$business->id,
            $this->defaultProjectionsRangeValue
        );

        return view(
            'business.projections',
            compact('business', 'rangeArray', 'currentProjectionsRange')
        );
    }

    public function updateData(Request $request)
    {
        $response = [
            'error' => [],
            'html' => [],
        ];

        $rangeValue = $request->rangeValue;
        $businessId = $request->businessId;

        if (!$rangeValue) {
            $response['error'][] = 'Range value not set';
        } else {
            session(['projectionRangeValue_'.$businessId => $rangeValue]);
        }

        $business = Business::find($businessId);

        $addDateStep = 'addDay';
        if($rangeValue == 7) {
            $addDateStep = 'addWeek';
        }
        if($rangeValue == 31) {
            $addDateStep = 'addMonth';
        }
        $entries_to_show = 14;
        $start_date = $today = Carbon::now();
        // start date is shown, so adjust end_date -1 to compensate
        $end_date = Carbon::now()->$addDateStep($entries_to_show - 1);

        $dates = array();
        for ($date = $start_date; $date < $end_date; $date->$addDateStep()) {
            $dates[] = $date->format('Y-m-d');
        }
        $rangeArray = $this->getRangeArray();
        $allocations = self::allocationsByDate($business);

        $response['html'] = view('business.projections-table')
            ->with(
                compact(
                    'allocations',
                    'business',
                    'dates',
                    'today',
                    'start_date',
                    'end_date',
                    'rangeArray'
                )
            )->render();

        return response()->json($response);
    }

    private function getRangeArray()
    {
        return [
            1 => 'Daily',
            7 => 'Weekly',
            31 => 'Monthly'
        ];
    }

    public function allocationsByDate(Business $business)
    {
        /**
         *  structure as follows
         *
         *  "${account_id}" => dates (collection) {
         *      "${Y-m-d}" => allocations (collection) {
         *          App\Models\Allocation
         *      }, ...
         *  }, ...
         */
        return $business->accounts->filter(
            function ($account) {
                return $account->type != BankAccount::ACCOUNT_TYPE_REVENUE;
            }
        )->mapWithKeys(
            function ($account) {
                // return key mapped accounts
                return [
                    $account->id => collect([
                        'account' => $account,
                        'dates' => $account->allocations->mapWithKeys(function ($allocation) {
                            // return key mapped allocations
                            return [$allocation->allocation_date->format('Y-m-d') => $allocation];
                        })
                    ])
                ];
            }
        );
    }

    /**
     * Gets the last calculated or entered value for the account,
     * if no existing values (ie. there have been no allocations),
     * returns null.
     */
    private function getLatestValueByAccount( BankAccount $account )
    {
        return $account->allocations->sortBy('allocation_date')->last();
    }


}
