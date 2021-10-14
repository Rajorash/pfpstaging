<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdvisorAdmin
 *
 * @property int $advisor_id
 * @property int $admin_id
 * @property-read \App\Models\User $advisors
 * @property-read \App\Models\User $regionalAdmin
 * @method static \Illuminate\Database\Eloquent\Builder|AdvisorAdmin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvisorAdmin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvisorAdmin query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvisorAdmin whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvisorAdmin whereAdvisorId($value)
 * @mixin \Eloquent
 */
class AdvisorAdmin extends Model
{
    use HasFactory;

    public function advisors(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function regionalAdmin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
