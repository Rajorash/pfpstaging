<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LicensesForAdvisors
 *
 * @property int $id
 * @property int $advisor_id
 * @property int $regional_admin_id
 * @property int $licenses
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $advisor
 * @property-read \App\Models\User $regionalAdmin
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors query()
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors whereAdvisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors whereLicenses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors whereRegionalAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicensesForAdvisors whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LicensesForAdvisors extends Model
{
    use HasFactory;

    public const DEFAULT_LICENSES_COUNT = 5;

    protected $fillable = [
        'licenses',
    ];

    public function advisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function regionalAdmin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'regional_admin_id');
    }
}
