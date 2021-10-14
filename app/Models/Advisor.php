<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Advisor
 *
 * @property int $id
 * @property int $user_id
 * @property int $seats
 * @property string|null $niche
 * @property string|null $tier
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereNiche($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereSeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Advisor whereUserId($value)
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
