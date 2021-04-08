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

class User extends Authenticatable
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'last_login_ip',
        'timezone'
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

    public function roles() {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function assignRole($role)
    {
        $this->roles()->sync($role, false);
    }

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
}
