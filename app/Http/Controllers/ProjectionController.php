<?php

namespace App\Http\Controllers;

use Carbon\CarbonPeriod;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\AccountFlow;
use App\Models\Allocation;
use App\Models\AllocationPercentage;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\Phase;
use Carbon\Carbon as Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use JamesMills\LaravelTimezone\Facades\Timezone;
use phpDocumentor\Reflection\Types\Array_;

class ProjectionController extends BusinessAllocationsController
{
    protected int $showEntries = 14;

    protected int $defaultCurrentRangeValue = self::RANGE_WEEKLY;

    protected string $pageDate = '';
    protected string $way = '';

    public const WAY_FUTURE = 'future';
    public const WAY_PAST = 'past';

    /**
     * Display a listing of the the accounts with projection.
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {

        $businessId = $request->business ?? null;
        $this->business = Business::findOrFail($businessId);
        $this->authorize('view', $this->business);

        $minDate = Carbon::now()->addWeek()->format('Y-m-d');
        $maxDate = Carbon::now()->addMonths(($this->showEntries - 1) * 3)->format('Y-m-d');

        return view(
            'business.projections', [
                'business' => $this->business,
                'rangeArray' => $this->getRangeArray(),
//                'endDate' => session()->get(
//                    'endDate_'.$this->business->id,
//                    Timezone::convertToLocal(Carbon::now()->addMonths(1), 'Y-m-d')
//                ),
                'minDate' => $minDate,
                'maxDate' => $maxDate,
                'currentProjectionsRange' => session()->get(
                    'projectionRangeValue_'.$this->business->id,
                    $this->defaultCurrentRangeValue
                ),
            ]
        );
    }

    public function updateData(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->projectionMode = self::PROJECTION_MODE_FORECAST;//set mode for Forecast page

        $this->periodInterval = $request->rangeValue ?? self::RANGE_WEEKLY;
        $this->pageDate = $request->pageDate ?? '';
        $this->way = $request->way ?? '';

        Carbon::macro('checkDate', function ($year, $month = null, $day = null) {
            if (isset($this)) {
                throw new \RuntimeException('Carbon::checkDate() must be called statically.');
            }

            if ($day === null) {
                [$year, $month, $day] = explode('-', $year);
            }

            return checkdate($month, $day, $year);
        });

        if ($this->way == self::WAY_FUTURE && $this->pageDate && Carbon::checkDate($this->pageDate)) {
            $request->startDate = Carbon::parse($this->pageDate)->format('Y-m-d');
        }
        if ($this->way == self::WAY_PAST && $this->pageDate && Carbon::checkDate($this->pageDate)) {
            $request->startDate = Carbon::parse($this->pageDate)
                ->subDays(($this->periodInterval * $this->forecastRowsPerPeriod) - 1)
                ->format('Y-m-d');
        }

        $this->accountsSubTypes = $this->getAccountsSubTypes();

        return parent::updateData($request);
    }

    protected function optimizationTableData($tableData, $period): array
    {
        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d');
        $periodDates = [];
        foreach ($period as $date) {
            $periodDates[] = $date->format('Y-m-d');
        }

        //remove unused data
        unset($tableData[BankAccount::ACCOUNT_TYPE_REVENUE]);
        foreach ($tableData as $accountType => $accountsArray) {
            foreach ($accountsArray as $accountId => $accountData) {
                unset($accountData['flows']);
                unset($accountData['total_db']);
                unset($accountData['total']);
                unset($accountData['transfer']);
                unset($accountData['allocations']);
                $accountData['_dates'] = array_filter($accountData['_dates'], function ($key) use ($periodDates) {
                    return in_array($key, $periodDates);
                }, ARRAY_FILTER_USE_KEY);
                $tableData[$accountType][$accountId] = $accountData;
            }
        }

        if ($today != Arr::first($periodDates)) {
            $this->tableAttributes .= "data-left-date='".(Arr::first($periodDates))."'";
            $title = Carbon::parse(Arr::first($periodDates))
                    ->subDays(($this->periodInterval * $this->forecastRowsPerPeriod) - 1)
                    ->format('d/m/Y')
                .' - '
                .Carbon::parse(Arr::first($periodDates))->format('d/m/Y');
            $this->tableAttributes .= "data-left-date-title='".$title."'";
        }

        $title = Carbon::parse(Arr::last($periodDates))->format('d/m/Y')
            .' - '.Carbon::parse(Arr::first($periodDates))
                ->addDays(($this->periodInterval * $this->forecastRowsPerPeriod) - 1)
                ->format('d/m/Y');
        $this->tableAttributes .= "data-right-date='".(Arr::last($periodDates))."' data-right-date-title='".$title."'";

        return $tableData;
    }

    protected function getAccountsSubTypes(): array
    {
        return [
            '_dates' => [
                'title' => '_self',
                'class_tr' => 'bg-account',
                'class_th' => 'pl-4'
            ]
        ];
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function updateDataOld(Request $request): \Illuminate\Http\JsonResponse
    {
        $response = [
            'error' => [],
            'html' => [],
        ];
        $rangeValue = $businessId = $endData = null;
        $page = 1;
        $prevPage = $nextPage = $prevPageTitle = $nextPageTitle = null;

        if (isset($request->rangeValue)) {
            $rangeValue = $request->rangeValue;
        }
        if (isset($request->businessId)) {
            $businessId = $request->businessId;
        }
        if (isset($request->endDate)) {
            $endData = $request->endDate;
        }
        if (isset($request->page)) {
            $page = intval($request->page);
        }
        if (!$page) {
            $page = 1;
        }

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
                $end_date = Carbon::now()->addMonths(($this->showEntries - 1) * 3);
            }
        }

//        if (isset($request->recalculateAll) && $request->recalculateAll == '1') {
//            $startDate = session()->get('startDate_'.$businessId,
//                Carbon::parse(Timezone::convertToLocal(Carbon::now(), 'Y-m-d')));
//            $AllocationsCalendarController = new AllocationsCalendar();
//            $AllocationsCalendarController->pushRecurringTransactionData($businessId, $startDate, $end_date, false);
//        }

        $datesAll = array();
        if ($rangeValue == self::RANGE_QUARTERLY) {
            for ($date = $start_date; $date < $end_date; $date->addMonth(3)) {
                $datesAll[] = $date->format('Y-m-d');
            }
        } else {
            for ($date = $start_date; $date < $end_date; $date->$addDateStep()) {
                $datesAll[] = $date->format('Y-m-d');
            }
        }
        $rangeArray = $this->getRangeArray();
        $allocations = self::allocationsByDate($business);

        $slices = [];
        if (count($datesAll) > $this->showEntries) {
            $slices = array_chunk($datesAll, $this->showEntries - 1);
            $dates = array_slice($datesAll, ($page > 1 ? ($this->showEntries - 1) * ($page - 1) : 0),
                $this->showEntries);

            $datesPrev = $datesNext = [];
            $currentPosition = array_search(Arr::first($dates), $datesAll);
            if ($page >= 2) {
                $datesPrev = array_slice($datesAll,
                    ($currentPosition + 1 - $this->showEntries),
                    $this->showEntries);
            }

            if ($page < count($slices)) {
                $datesNext = array_slice($datesAll,
                    (array_search(Arr::last($dates), $datesAll)),
                    $this->showEntries);
            }

            $prevPage = $page - 1;
            if ($prevPage <= 0) {
                $prevPage = null;
            } elseif (!empty($datesPrev)) {
                $prevPageTitle =
                    Carbon::parse(Arr::first($datesPrev))->format('j M Y')
                    .' - '
                    .Carbon::parse(Arr::last($datesPrev))->format('j M Y');
            }

            $nextPage = $page + 1;
            if ($nextPage > count($slices)) {
                $nextPage = null;
            } elseif (!empty($datesNext)) {
                $nextPageTitle =
                    Carbon::parse(Arr::first($datesNext))->format('j M Y')
                    .' - '
                    .Carbon::parse(Arr::last($datesNext))->format('j M Y');
            }

        } else {
            $dates = $datesAll;
        }

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
                    'rangeArray',
                    'slices',
                    'prevPage',
                    'prevPageTitle',
                    'nextPage',
                    'nextPageTitle'
                )
            )->render();

        return response()->json($response);
    }

    /**
     * @return string[]
     */
    protected function getRangeArray(): array
    {
        return [
            self::RANGE_DAILY => 'Daily',
            self::RANGE_WEEKLY => 'Weekly',
            self::RANGE_MONTHLY => 'Monthly',
            self::RANGE_QUARTERLY => 'Quarterly'
        ];
    }

//    /**
//     * @param  Business  $business
//     * @return BankAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
//     */
//    public function allocationsByDate(Business $business)
//    {
//        /**
//         *  structure as follows
//         *
//         *  "${account_id}" => dates (collection) {
//         *      "${Y-m-d}" => allocations (collection) {
//         *          App\Models\Allocation
//         *      }, ...
//         *  }, ...
//         */
//
//        $accounts = $this->sortAccountsByType($business->accounts);
//
//        return $accounts->filter(
//            function ($account) {
//                return $account->type != BankAccount::ACCOUNT_TYPE_REVENUE;
//            }
//        )->mapWithKeys(
//            function ($account) {
//                // return key mapped accounts
//                return [
//                    $account->id => collect([
//                        'account' => $account,
//                        'type' => $account->type,
//                        'dates' => $account->allocations->mapWithKeys(function ($allocation) {
//                            // return key mapped allocations
//                            return [$allocation->allocation_date->format('Y-m-d') => $allocation];
//                        }),
//                        'last_val' => $account->allocations->last()->amount ?? null
//                    ])
//                ];
//            }
//        );
//    }

//    /**
//     * Gets the last calculated or entered value for the account,
//     * if no existing values (ie. there have been no allocations),
//     * returns null.
//     */
//    private function getLatestValueByAccount(BankAccount $account)
//    {
//        return $account->allocations->sortBy('allocation_date')->last();
//    }

//    private function sortAccountsByType($accounts)
//    {
//        // returns ['revenue', 'pretotal', 'salestax', 'prereal', 'postreal']
//        $order = BankAccount::type_list();
//
//        return $accounts->sortBy(function ($account) use ($order) {
//            return array_search($account->type, $order);
//        });
//    }
}
