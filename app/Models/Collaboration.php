<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Collaboration
 *
 * @property int $id
 * @property int $advisor_id
 * @property int $business_id
 * @property string|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Advisor $advisor
 * @property-read Business $business
 * @method static Builder|Collaboration newModelQuery()
 * @method static Builder|Collaboration newQuery()
 * @method static Builder|Collaboration query()
 * @method static Builder|Collaboration whereAdvisorId($value)
 * @method static Builder|Collaboration whereBusinessId($value)
 * @method static Builder|Collaboration whereCreatedAt($value)
 * @method static Builder|Collaboration whereDeletedAt($value)
 * @method static Builder|Collaboration whereExpiresAt($value)
 * @method static Builder|Collaboration whereId($value)
 * @method static Builder|Collaboration whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Collaboration extends Model
{
    protected $fillable = ['advisor_id', 'business_id'];

    public function advisor()
    {
        return $this->hasOne(Advisor::class, 'id', 'advisor_id');
    }

    public function business()
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
