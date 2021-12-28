<?php

namespace App\Models;

use App\Traits\HasTags;
use App\Traits\RecurringAndPipeline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Pipeline
 *
 * @property int $id
 * @property int $business_id
 * @property string $title
 * @property string|null $notes
 * @property int $certainty
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
 * @property bool $recurring
 * @property-read \App\Models\BankAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline newQuery()
 * @method static \Illuminate\Database\Query\Builder|Pipeline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereCertainty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereRepeatEveryNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereRepeatEveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereRepeatRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pipeline whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|Pipeline withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Pipeline withoutTrashed()
 * @mixin \Eloquent
 */
class Pipeline extends Model
{
    use HasFactory, SoftDeletes, RecurringAndPipeline, HasTags;

    public const REPEAT_DAY = 'day';
    public const REPEAT_WEEK = 'week';
    public const REPEAT_MONTH = 'month';
    public const REPEAT_YEAR = 'year';
    public const REPEAT_DEFAULT = self::REPEAT_WEEK;

    public const DEFAULT_CERTAINTY = 70;

    protected $table = 'pipeline';

    protected $fillable = [
        'title',
        'notes',
        'certainty',
        'description',
        'value',
        'date_start',
        'date_end',
        'repeat_every_number',
        'repeat_every_type',
        'repeat_rules',
        'recurring'
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'repeat_every_number' => 'integer',
        'repeat_rules' => 'json',
        'value' => 'float',
        'certainty' => 'integer',
        'recurring' => 'boolean'
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'business_id', 'id');
    }

}
