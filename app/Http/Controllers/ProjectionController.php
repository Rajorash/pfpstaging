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
    /**
     * Display a listing of the the accounts with projection.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        $this->authorize('view', $business);

        $scale = 'addDay';
        $start_date = $today = Carbon::now();
        $end_date = Carbon::now()->$scale(14);

        $dates = array();
        for ($date = $start_date; $date <= $end_date; $date->$scale(1)) {
            $dates[] = $date->format('Y-m-d');
        }

        $allocations = self::allocationsByDate($business);
        // dd($allocations);

        return view('projections.show', compact('allocations', 'business', 'dates', 'today', 'start_date', 'end_date'));
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
        return $business->accounts->mapWithKeys(function ($account) {
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
        });
    }

}
