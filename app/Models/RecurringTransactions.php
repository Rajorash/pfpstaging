<?php

namespace App\Models;

use App\Traits\RecurringAndPipeline;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\RecurringTransactions
 *
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read AccountFlow $accountFlow
 * @method static Builder|RecurringTransactions newModelQuery()
 * @method static Builder|RecurringTransactions newQuery()
 * @method static \Illuminate\Database\Query\Builder|RecurringTransactions onlyTrashed()
 * @method static Builder|RecurringTransactions query()
 * @method static Builder|RecurringTransactions whereAccountId($value)
 * @method static Builder|RecurringTransactions whereCreatedAt($value)
 * @method static Builder|RecurringTransactions whereDateEnd($value)
 * @method static Builder|RecurringTransactions whereDateStart($value)
 * @method static Builder|RecurringTransactions whereDeletedAt($value)
 * @method static Builder|RecurringTransactions whereDescription($value)
 * @method static Builder|RecurringTransactions whereId($value)
 * @method static Builder|RecurringTransactions whereRepeatEveryNumber($value)
 * @method static Builder|RecurringTransactions whereRepeatEveryType($value)
 * @method static Builder|RecurringTransactions whereRepeatRules($value)
 * @method static Builder|RecurringTransactions whereTitle($value)
 * @method static Builder|RecurringTransactions whereUpdatedAt($value)
 * @method static Builder|RecurringTransactions whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|RecurringTransactions withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RecurringTransactions withoutTrashed()
 * @mixin Eloquent
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
