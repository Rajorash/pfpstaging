<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdvisorAdmin
 *
 * @property int $advisor_id
 * @property int $admin_id
 * @property-read User $advisors
 * @property-read User $regionalAdmin
 * @method static Builder|AdvisorAdmin newModelQuery()
 * @method static Builder|AdvisorAdmin newQuery()
 * @method static Builder|AdvisorAdmin query()
 * @method static Builder|AdvisorAdmin whereAdminId($value)
 * @method static Builder|AdvisorAdmin whereAdvisorId($value)
 * @mixin Eloquent
 */
class AdvisorAdmin extends Model
{
    use HasFactory;

    public function advisors()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function regionalAdmin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
