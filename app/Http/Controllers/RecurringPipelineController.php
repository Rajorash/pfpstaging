<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Models\RecurringTransactions;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use JamesMills\LaravelTimezone\Timezone;

class RecurringPipelineController
{
    public const REPEAT_DAY = 'day';
    public const REPEAT_WEEK = 'week';
    public const REPEAT_MONTH = 'month';
    public const REPEAT_YEAR = 'year';
    public const REPEAT_DEFAULT = self::REPEAT_WEEK;

    /** Get compiled forecast for Flow
     * @param  Collection  $recurringTransactionsArray
     * @param  string|null  $dateStart
     * @param  string|null  $dateEnd
     * @return array
     */
    public function getAllFlowsForecasts(
        Collection $recurringTransactionsArray,
        string $dateStart = null,
        string $dateEnd = null
    ): array {
        $result = [];
        foreach ($recurringTransactionsArray as $recurringTransactions) {
            $result[$recurringTransactions->title] =
                [
                    'forecast' => $this->getForecast($recurringTransactions, $dateStart, $dateEnd),
                    'title' => $recurringTransactions->title,
                    'description' => $recurringTransactions->description,
                ];
        }

        ksort($result);

        return $result;
    }


    /**
     * @param  RecurringTransactions|Pipeline  $records
     * @param  string|null  $periodDateStart
     * @param  string|null  $periodDateEnd
     * @return array
     */
    public function getForecast(
        $records,
        string $periodDateStart = null,
        string $periodDateEnd = null
    ): array {
        $datesRangeFromUserLimit = [];
        $intersection = [];
        $days = [];

        //get dates from recurringTransactions
        $startDate = Carbon::parse($records->date_start);
        $endDate = $records->date_end ? Carbon::parse($records->date_end) : null;
        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s');

        //if end date not set - use periodDateEnd or calculate from now
        if (!$endDate) {
            $endDate = $periodDateEnd ? Carbon::parse($periodDateEnd)
                : ($records->repeat_every_type == self::REPEAT_YEAR
                    ? Carbon::parse($today)->addYears(3)
                    : Carbon::parse($today)->addMonths(3)
                );
        }

        $datesRangeFromRecurringTransaction = $this->getTimestampsFromPeriod($startDate, $endDate);

        if ($periodDateStart && $periodDateEnd) {
            $datesRangeFromUserLimit = $this->getTimestampsFromPeriod($periodDateStart, $periodDateEnd);
            $intersection = array_intersect($datesRangeFromRecurringTransaction, $datesRangeFromUserLimit);
        } else {
            $intersection = $datesRangeFromRecurringTransaction;
        }

        if (!empty($intersection)) {
            sort($intersection);

            //intersected date
            $allowedPeriodStart = Carbon::createFromTimestamp(Arr::first($intersection));
            $allowedPeriodEnd = Carbon::createFromTimestamp(Arr::last($intersection));

            if (isset($records->repeat_rules['days'])) {
                foreach ($records->repeat_rules['days'] as $dayName) {
                    $this->_getDaysByName($days,
                        $dayName,
                        $records->repeat_every_number,
                        $records->repeat_every_type,
                        $allowedPeriodStart,
                        $allowedPeriodEnd,
                        $records->value
                    );
                }
            } else {
                $this->_getDaysByName($days,
                    null,
                    $records->repeat_every_number,
                    $records->repeat_every_type,
                    $allowedPeriodStart,
                    $allowedPeriodEnd,
                    $records->value
                );
            }

            ksort($days);
        }

        return $days;
    }

    /**return all timestamps from period
     * @param  string  $start
     * @param  string  $end
     * @return array
     */
    private function getTimestampsFromPeriod(
        string $start,
        string $end
    ): array {
        $dates = [];
        if (Carbon::parse($start)->lte(Carbon::parse($end))) {
            $period = CarbonPeriod::create($start, $end);
            foreach ($period->toArray() as $carbonDate) {
                $dates[] = $carbonDate->timestamp;
            }
        }

        return $dates;
    }

    /** retrun days by text name
     * @param  array  $days
     * @param  ?string  $dayName
     * @param  int  $intervalValue
     * @param  string  $intervalType
     * @param  Carbon  $startDate
     * @param  Carbon  $endDate
     * @param  float  $value
     * @return void
     */
    private function _getDaysByName(
        array &$days,
        ?string $dayName,
        int $intervalValue,
        string $intervalType,
        Carbon $startDate,
        Carbon $endDate,
        float $value
    ) {
        if ($value) {
            $startDate = Carbon::parse($startDate);

            if ($intervalType == self::REPEAT_DAY) {
                for ($date = $startDate; $date->lte($endDate); $date->addDays($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }

            if ($intervalType == self::REPEAT_WEEK) {
                $startDate = $startDate->modify('this '.$dayName);
                for ($date = $startDate; $date->lte($endDate); $date->addWeeks($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }

            if ($intervalType == self::REPEAT_MONTH) {
                for ($date = $startDate; $date->lte($endDate); $date->addMonths($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }

            if ($intervalType == self::REPEAT_YEAR) {
                for ($date = $startDate; $date->lte($endDate); $date->addYears($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }
        }
    }
}
