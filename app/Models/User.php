<?php

namespace App\Models;

use App\Interfaces\RoleInterface;
use App\Traits\HasUserRoles;
use App\Traits\UserLicenseFunctions;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $title
 * @property string|null $responsibility
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $password_changed_at
 * @property string|null $timezone
 * @property Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $active
 * @property-read Collection|Business[] $activeLicenses
 * @property-read int|null $active_licenses_count
 * @property-read Advisor|null $advisor
 * @property-read Collection|User[] $advisorByClient
 * @property-read int|null $advisor_by_client_count
 * @property-read Collection|User[] $advisorsByRegionalAdmin
 * @property-read int|null $advisors_by_regional_admin_count
 * @property-read Collection|LicensesForAdvisors[] $advisorsLicenses
 * @property-read int|null $advisors_licenses_count
 * @property-read Collection|Business[] $businesses
 * @property-read int|null $businesses_count
 * @property-read Collection|User[] $clientsByAdvisor
 * @property-read int|null $clients_by_advisor_count
 * @property-read Collection|Collaboration[] $collaborations
 * @property-read int|null $collaborations_count
 * @property-read Team|null $currentTeam
 * @property-read string $niche
 * @property-read string $profile_photo_url
 * @property-read int $seats
 * @property-read void $tier
 * @property-read Collection|Business[] $licenses
 * @property-read int|null $licenses_count
 * @property-read Collection|Business[] $notActiveLicenses
 * @property-read int|null $not_active_licenses_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Team[] $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read Collection|User[] $regionalAdminByAdvisor
 * @property-read int|null $regional_admin_by_advisor_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property-read Collection|Team[] $teams
 * @property-read int|null $teams_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereActive($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereCurrentTeamId($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastLoginAt($value)
 * @method static Builder|User whereLastLoginIp($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePasswordChangedAt($value)
 * @method static Builder|User whereProfilePhotoPath($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereResponsibility($value)
 * @method static Builder|User whereTimezone($value)
 * @method static Builder|User whereTitle($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable implements RoleInterface, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use HasUserRoles;
    use UserLicenseFunctions;
    use Notifiable;
    use TwoFactorAuthenticatable;

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
    public function businesses()
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function collaborations()
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

    public function advisor()
    {
        return $this->hasOne(Advisor::class, 'user_id');
    }

    public function isActive()
    {
        if ($this->isSuperAdmin() || $this->isRegionalAdmin()) {
            return $this->active;
        } else {
            //TODO - active based on licenses
        }

        return $this->active;
    }
}
