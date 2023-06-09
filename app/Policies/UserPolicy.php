<?php

namespace App\Policies;

use App\Models\License;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

//        //a Regional Admin can view only own Advisor
        if ($user->isRegionalAdmin()) {
            if ($user->id == $model->regionalAdminByAdvisor->pluck('id')->first()) {
                return true;
            }
            // if regional admin and advisor share a license
            $shared_licenses = License::whereAdvisorId($model->id)
                ->whereRegionaladminId($user->id)
                ->get();

            if ($shared_licenses->isNotEmpty()) {
                return true;
            }

        }

        //a Regional Admin can update only own Advisor
        if ($user->isAdvisor() && $user->id == $model->advisorByClient->pluck('id')->first()) {
            return true;
        }

        // a user can view themselves
        if ($user->id === $model->id) {
            return true;
        }

        // an advisor can view any clients they have
        if ($model->businesses->map->license->pluck('advisor_id')->contains($user->id)) {
            return true;
        }

        // an advisor can view any clients they are collaborating on
        if ($model->businesses->map->collaboration->pluck('advisor_id')->contains($user->id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $roles = $user->roles->pluck('name')->toArray();
        if (in_array(User::ROLE_ADMIN, $roles) || in_array(User::ROLE_ADVISOR, $roles)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        //a Regional Admin can update only own Advisor
        if ($user->isRegionalAdmin() && $user->id == $model->regionalAdminByAdvisor->pluck('id')->first()) {
            return true;
        }

        // users can update themselves
        if ($user->id === $model->id) {
            return true;
        }

        // advisors can update any clients they have
        if ($model->businesses->map->license->pluck('advisor_id')->contains($user->id)) {
            return true;
        }

        // advisors can update any clients they are collaborating on
        if ($model->businesses->map->collaboration->pluck('advisor_id')->contains($user->id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function edit(User $user, User $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // users can update themselves
        if ($user->id === $model->id) {
            return true;
        }

        //a Regional Admin can update only own Advisor
        if ($user->isAdvisor() && $user->id == $model->advisorByClient->pluck('id')->first()) {
            return true;
        }

        // advisors can update any clients they have
        if ($model->businesses->map->license->pluck('advisor_id')->contains($user->id)) {
            return true;
        }

//        // advisors can update any clients they are collaborating on
//        if ($model->businesses->map->collaboration->pluck('advisor_id')->contains($user->id)) {
//            return true;
//        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return false;
    }

    /**
     * Determine whether the user can see a users list
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function indexUsers(User $user, User $model)
    {
        if ($user->isSuperAdmin() || $user->isRegionalAdmin() || $user->isAdvisor()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can see a businesses list
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function indexBusinesses(User $user, User $model)
    {
        if ($user->isSuperAdmin() || $user->isAdvisor()) {
            return true;
        }

        return false;
    }
}
