<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Collaboration
 *
 * @property int $id
 * @property int $advisor_id
 * @property int $business_id
 * @property string|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Advisor|null $advisor
 * @property-read \App\Models\Business $business
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration query()
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration whereAdvisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collaboration whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Collaboration extends Model
{
    protected $fillable = ['advisor_id', 'business_id'];

    public function advisor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Advisor::class, 'id', 'advisor_id');
    }

    public function business(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function expiresAt()
    {
        if (is_null($this->expires_at)) {
            return false;
        }

        return $this->expires_at;
    }
}
