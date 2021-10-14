<?php

namespace App\Traits;

use App\Models\Pipeline;
use App\Models\RecurringTransactions;
use Carbon\Carbon;
use Illuminate\Support\Arr;

trait RecurringAndPipeline
{
    public static function getRepeatTimeArray()
    {
        return [
            self::REPEAT_DAY => __('Day'),
            self::REPEAT_WEEK => __('Week'),
            self::REPEAT_MONTH => __('Month'),
            self::REPEAT_YEAR => __('Year')
        ];
    }

    public static function getWeekDaysArray()
    {
        return [
            'monday' => __('Monday'),
            'tuesday' => __('Tuesday'),
            'wednesday' => __('Wednesday'),
            'thursday' => __('Thursday'),
            'friday' => __('Friday'),
            'saturday' => __('Saturday'),
            'sunday' => __('Sunday')
        ];
    }
}
