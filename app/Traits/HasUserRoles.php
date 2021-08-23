<?php

namespace App\Traits;

use App\Models\Advisor;
use App\Models\Role;
use App\Models\User;
trait HasUserRoles
{

    public function roles()
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
    public function isSuperAdmin()
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
    public function isRegionalAdmin()
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
    public function isAdvisor()
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
    public function isClient()
    {
        if (is_null($this->roles->firstWhere('name', self::ROLE_CLIENT))) {
            return false;
        }

        return true;
    }

}
