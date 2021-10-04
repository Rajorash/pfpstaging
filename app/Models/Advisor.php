<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Advisor
 *
 * @property int $id
 * @property int $user_id
 * @property int $seats
 * @property string|null $niche
 * @property string|null $tier
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $user
 * @method static Builder|Advisor newModelQuery()
 * @method static Builder|Advisor newQuery()
 * @method static Builder|Advisor query()
 * @method static Builder|Advisor whereCreatedAt($value)
 * @method static Builder|Advisor whereDeletedAt($value)
 * @method static Builder|Advisor whereId($value)
 * @method static Builder|Advisor whereNiche($value)
 * @method static Builder|Advisor whereSeats($value)
 * @method static Builder|Advisor whereTier($value)
 * @method static Builder|Advisor whereUpdatedAt($value)
 * @method static Builder|Advisor whereUserId($value)
 * @mixin \Eloquent
 */
class Advisor extends Model
{
    protected $fillable = [
        'user_id',
        'seats',
        'niche',
        'tier'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
