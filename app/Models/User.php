<?php

namespace App\Models;

use App\Interfaces\RoleInterface;
use App\Traits\HasUserRoles;
use App\Traits\UserLicenseFunctions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

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
            ->where(function($query) {
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
