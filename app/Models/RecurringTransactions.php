<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property AccountFlow $account_id
 * @property string $title
 * @property string $description
 * @property float $value
 * @property Carbon $date_start
 * @property Carbon|null $date_end
 * @property integer $repeat_every_number
 * @property string $repeat_every_type
 * @property array $repeat_rules
 */
class RecurringTransactions extends Model
{
    use HasFactory, SoftDeletes;

    public const REPEAT_DAY = 'day';
    public const REPEAT_WEEK = 'week';
    public const REPEAT_MONTH = 'month';
    public const REPEAT_YEAR = 'year';
    public const REPEAT_DEFAULT = self::REPEAT_WEEK;

    protected $table = 'recurring';

    protected $fillable = [
        'title',
        'description',
        'value',
        'date_start',
        'date_end',
        'repeat_every_number',
        'repeat_every_type',
        'repeat_rules'
    ];

    protected $guarded = [
        'account_id'
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'repeat_every_number' => 'integer',
        'repeat_rules' => 'json',
        'value' => 'float'
    ];

    public function accountFlow()
    {
        return $this->belongsTo(AccountFlow::class, 'account_id', 'id');
    }

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
