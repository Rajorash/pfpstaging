<?php

namespace App\Traits;

use App\Models\Advisor;
use App\Models\Role;
use App\Models\User;
trait HasUserRoles
{

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function assignRole(Role $role)
    {
        $this->roles()->sync($role, false);

        if($role->name == 'advisor' && !Advisor::where('user_id', '=', $this->id)->first() ) {
            Advisor::create(['user_id' => $this->id]);
        }
    }

    public function advisorsByRegionalAdmin(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'advisor_admins',
            'admin_id',
            'advisor_id'
        );
    }

    public function regionalAdminByAdvisor(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'advisor_admins',
            'advisor_id',
            'admin_id'
        );
    }

    public function advisorByClient(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'client_advisors',
            'client_id',
            'advisor_id'
        );
    }

    public function clientsByAdvisor(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
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

    /**
     * returns the permissions a user has based on
     * roles, by name.
     *
     * @return void
     */
    public function permissions()
    {
        return $this->roles->map->permissions->flatten()->pluck('name')->unique();
    }

    /**
     * Returns true is a user has the SuperAdmin role
     *
     * @return boolean
     */
    public function isSuperAdmin(): bool
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_SUPERADMIN))) {
            return false;
        }

        return true;
    }

    /**
     * Returns true is a user has the Regional Admin role
     *
     * @return boolean
     */
    public function isRegionalAdmin(): bool
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_ADMIN))) {
            return false;
        }

        return true;
    }

    /**
     * Returns true is a user has the Advisor role
     *
     * @return boolean
     */
    public function isAdvisor(): bool
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_ADVISOR))) {
            return false;
        }

        return true;
    }

    /**
     * Returns true is a user has the Client role
     *
     * @return boolean
     */
    public function isClient(): bool
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_CLIENT))) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if the user has the passed role name
     *
     * @param [type] $role_name
     * @return boolean
     */
    public function hasRole($role_name): bool
    {
        if (is_null($this->roles->firstWhere('name', $role_name))) {
            return false;
        }

        return true;
    }

}
