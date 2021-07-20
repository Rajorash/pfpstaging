<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public const ROLE_SUPERADMIN = 'superuser';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_ADVISOR = 'advisor';
    public const ROLE_CLIENT = 'client';

    public const ROLE_IDS = [
        self::ROLE_SUPERADMIN => 1,
        self::ROLE_ADMIN => 2,
        self::ROLE_ADVISOR => 3,
        self::ROLE_CLIENT => 4
    ];

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
    ];

    /**
     * Return all businesses related to the user
     */
    public function businesses()
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function assignRole(Role $role)
    {
        $this->roles()->sync($role, false);
    }

    public function licenses()
    {
        return $this->belongsToMany(Business::class, 'licenses', 'advisor_id', 'business_id');
    }

    public function activeLicenses()
    {
        return $this->belongsToMany(Business::class, 'licenses', 'advisor_id', 'business_id')
            ->where('active', true);
    }

    public function notActiveLicenses()
    {
        return $this->belongsToMany(Business::class, 'licenses', 'advisor_id', 'business_id')
            ->where('active', false);
    }

    public function collaborations()
    {
        return $this->belongsToMany(Business::class, 'collaborations', 'advisor_id', 'business_id')
            ->where(function ($query) {
                $query->where('collaborations.expires_at', '>', date('Y-m-d H:i:s'))
                    ->orWhere('collaborations.expires_at', '=', null);
            });
    }

    public function assignLicense($business)
    {
        $this->licenses()->sync($business, false);
    }

    public function advisorsByRegionalAdmin()
    {
        return $this->belongsToMany(
            User::class,
            'advisor_admins',
            'admin_id',
            'advisor_id'
        );
    }

    public function regionalAdminByAdvisor()
    {
        return $this->belongsToMany(
            User::class,
            'advisor_admins',
            'advisor_id',
            'admin_id'
        );
    }

    public function advisorByClient()
    {
        return $this->belongsToMany(
            User::class,
            'client_advisors',
            'client_id',
            'advisor_id'
        );
    }

    public function clientsByAdvisor()
    {
        return $this->belongsToMany(
            User::class,
            'client_advisors',
            'advisor_id',
            'client_id'
        );
    }

//    public function myAdvisors()
//    {
//        return $this->hasManyThrough(User::class, AdvisorAdmin::class
//            ,'admin_id','id'
//            ,'id','advisor_id');
//    }

    public function permissions()
    {
        // return the permissions associated with any assigned roles, by name.
        return $this->roles->map->permissions->flatten()->pluck('name')->unique();
    }

    public function isSuperAdmin()
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_SUPERADMIN))) {
            return false;
        }

        return true;
    }

    public function isRegionalAdmin()
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_ADMIN))) {
            return false;
        }

        return true;
    }

    public function isAdvisor()
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_ADVISOR))) {
            return false;
        }

        return true;
    }

    public function isClient()
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_CLIENT))) {
            return false;
        }

        return true;
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

    public function advisorsLicenses()
    {
        return $this->hasMany(
            LicensesForAdvisors::class,
            'advisor_id',
            'id');
    }


//    public function relatedToAdmin()
//    {
//        if ($this->isAdvisor()) {
//            return $this->hasOneThrough(
//                User::class,
//                Advisor::class,
//                'regional_admin_id',
//                'id',
//                'id',
//                'id'
//            );
//        } else {
//            return null;
//        }
//    }
}
