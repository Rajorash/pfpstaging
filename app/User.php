<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'last_login_at', 'last_login_ip', 'timezone' 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime', 'last_login_at' => 'datetime', 'password_changed_at' => 'datetime', 
    ];

    /**
     * Return all businesses related to the user
     */
    public function businesses()
    {
        return $this->hasMany('App\Business', 'owner_id');
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

}
