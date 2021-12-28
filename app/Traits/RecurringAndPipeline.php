<?php

namespace App\Traits;

trait RecurringAndPipeline
{
    public static function getRepeatTimeArray(): array
    {
        return [
            self::REPEAT_DAY => __('Day'),
            self::REPEAT_WEEK => __('Week'),
            self::REPEAT_MONTH => __('Month'),
            self::REPEAT_YEAR => __('Year')
        ];
    }

    public static function getWeekDaysArray(): array
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
