<?php

namespace App\Models;

use App\Interfaces\RoleInterface;
use App\Traits\HasUserRoles;
use App\Traits\UserLicenseFunctions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $title
 * @property string|null $responsibility
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $password_changed_at
 * @property string|null $timezone
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $active
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Business[] $activeLicenses
 * @property-read int|null $active_licenses_count
 * @property-read \App\Models\Advisor|null $advisor
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $advisorByClient
 * @property-read int|null $advisor_by_client_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $advisorsByRegionalAdmin
 * @property-read int|null $advisors_by_regional_admin_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LicensesForAdvisors[] $advisorsLicenses
 * @property-read int|null $advisors_licenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Business[] $businesses
 * @property-read int|null $businesses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $clientsByAdvisor
 * @property-read int|null $clients_by_advisor_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Collaboration[] $collaborations
 * @property-read int|null $collaborations_count
 * @property-read \App\Models\Team|null $currentTeam
 * @property-read string|boolean $niche
 * @property-read string $profile_photo_url
 * @property-read int|boolean $seats
 * @property-read string|null|boolean $tier
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Business[] $licenses
 * @property-read int|null $licenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Business[] $notActiveLicenses
 * @property-read int|null $not_active_licenses_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $regionalAdminByAdvisor
 * @property-read int|null $regional_admin_by_advisor_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePasswordChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereResponsibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements RoleInterface, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasTeams;
    use HasUserRoles;
    use UserLicenseFunctions;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'title',
        'responsibility',
        'password',
        'last_login_at',
        'last_login_ip',
        'timezone',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'seats',
        'tier',
        'niche',
    ];

    /**
     * Return all businesses related to the user
     */
    public function businesses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function collaborations(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Collaboration::class, Advisor::class, 'user_id', 'advisor_id');
    }

    public function activeCollaborations()
    {
        return $this->collaborations()
            ->where(function ($query) {
                $query->where('collaborations.expires_at', '>', date('Y-m-d H:i:s'))
                    ->orWhere('collaborations.expires_at', '=', null);
            });
    }

    public function advisor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Advisor::class, 'user_id');
    }

    public function isActive(): int
    {
        if ($this->isSuperAdmin() || $this->isRegionalAdmin()) {
            return $this->active;
        } else {
            //TODO - active based on licenses
        }

        return $this->active;
    }
}
