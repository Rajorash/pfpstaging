<?php

namespace App\Models;

use App\Traits\RecurringAndPipeline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RecurringTransactions
 *
 * @property int $id
 * @property int $account_id
 * @property string $title
 * @property string|null $description
 * @property float $value
 * @property \Illuminate\Support\Carbon $date_start
 * @property \Illuminate\Support\Carbon|null $date_end
 * @property int $repeat_every_number
 * @property string $repeat_every_type
 * @property array|null $repeat_rules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AccountFlow $accountFlow
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions newQuery()
 * @method static \Illuminate\Database\Query\Builder|RecurringTransactions onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions query()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereRepeatEveryNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereRepeatEveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereRepeatRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringTransactions whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|RecurringTransactions withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RecurringTransactions withoutTrashed()
 * @mixin \Eloquent
 */
class RecurringTransactions extends Model
{
    use HasFactory, SoftDeletes, RecurringAndPipeline;

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

    public function accountFlow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AccountFlow::class, 'account_id', 'id');
    }
}
