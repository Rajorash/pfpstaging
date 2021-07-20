<?php

namespace App\Traits;

trait HasUserRoles
{

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function assignRole(Role $role)
    {
        $this->roles()->sync($role, false);
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

}
