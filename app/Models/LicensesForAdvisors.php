<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\LicensesForAdvisors
 *
 * @property int $id
 * @property int $advisor_id
 * @property int $regional_admin_id
 * @property int $licenses
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $advisor
 * @property-read User $regionalAdmin
 * @method static Builder|LicensesForAdvisors newModelQuery()
 * @method static Builder|LicensesForAdvisors newQuery()
 * @method static Builder|LicensesForAdvisors query()
 * @method static Builder|LicensesForAdvisors whereAdvisorId($value)
 * @method static Builder|LicensesForAdvisors whereCreatedAt($value)
 * @method static Builder|LicensesForAdvisors whereId($value)
 * @method static Builder|LicensesForAdvisors whereLicenses($value)
 * @method static Builder|LicensesForAdvisors whereRegionalAdminId($value)
 * @method static Builder|LicensesForAdvisors whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LicensesForAdvisors extends Model
{
    use HasFactory;

    public const DEFAULT_LICENSES_COUNT = 5;

    protected $fillable = [
        'licenses',
    ];

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function regionalAdmin()
    {
        return $this->belongsTo(User::class, 'regional_admin_id');
    }
}
