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
use JamesMills\LaravelTimezone\Facades\Timezone;

class ProjectionController extends Controller
{
    protected $defaultProjectionsRangeValue = 7;

    public const RANGE_DAILY = 1;
    public const RANGE_WEEKLY = 7;
    public const RANGE_MONTHLY = 31;
    public const RANGE_QUARTERLY = 93;

    protected int $showEntries = 14;

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

        $minDate = Carbon::now()->addWeek()->format('Y-m-d');
        $maxDate = Carbon::now()->addMonth(($this->showEntries - 1) * 3)->format('Y-m-d');

        return view(
            'business.projections',
            compact('business', 'rangeArray',
                'currentProjectionsRange', 'minDate', 'maxDate')
        );
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateData(Request $request): \Illuminate\Http\JsonResponse
    {
        $response = [
            'error' => [],
            'html' => [],
        ];

        $rangeValue = $request->rangeValue;
        $businessId = $request->businessId;
        $endData = $request->endDate;

        if (!$rangeValue) {
            $response['error'][] = 'Range value not set';
        } else {
            session(['projectionRangeValue_'.$businessId => $rangeValue]);
        }

        $business = Business::find($businessId);

        $start_date = $today = Carbon::parse(Timezone::convertToLocal(Carbon::now(), 'Y-m-d'));
        $addDateStep = 'addDay';
        if ($rangeValue == self::RANGE_WEEKLY) {
            $addDateStep = 'addWeek';
        }
        if ($rangeValue == self::RANGE_MONTHLY) {
            $addDateStep = 'addMonth';
        }

        if ($endData) {
            //set end date by user
            $end_date = Carbon::parse($endData);
        } else {
            //default behaviour

            // start date is shown, so adjust end_date -1 to compensate
            $end_date = Carbon::now()->$addDateStep($this->showEntries - 1);

            if ($rangeValue == self::RANGE_QUARTERLY) {
                $end_date = Carbon::now()->addMonth(($this->showEntries - 1) * 3);
            }
        }

        if ($request->recalculateAll == '1') {
            $startDate = session()->get('startDate_'.$businessId,
                Carbon::parse(Timezone::convertToLocal(Carbon::now(), 'Y-m-d')));
            $AllocationsCalendarController = new AllocationsCalendar();
            $AllocationsCalendarController->pushRecurringTransactionData($businessId, $startDate, $end_date, false);
        }

        $dates = array();
        if ($rangeValue == self::RANGE_QUARTERLY) {
            for ($date = $start_date; $date < $end_date; $date->addMonth(3)) {
                $dates[] = $date->format('Y-m-d');
            }
        } else {
            for ($date = $start_date; $date < $end_date; $date->$addDateStep()) {
                $dates[] = $date->format('Y-m-d');
            }
        }
        $rangeArray = $this->getRangeArray();
        $allocations = self::allocationsByDate($business);

        $response['end_date'] = $end_date->format('Y-m-d');
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

    /**
     * @return string[]
     */
    private function getRangeArray(): array
    {
        return [
            self::RANGE_DAILY => 'Daily',
            self::RANGE_WEEKLY => 'Weekly',
            self::RANGE_MONTHLY => 'Monthly',
            self::RANGE_QUARTERLY => 'Quarterly'
        ];
    }

    /**
     * @param  Business  $business
     * @return BankAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
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
                        }),
                        'last_val' => $account->allocations->last()->amount ?? null
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
    private function getLatestValueByAccount(BankAccount $account)
    {
        return $account->allocations->sortBy('allocation_date')->last();
    }
}
