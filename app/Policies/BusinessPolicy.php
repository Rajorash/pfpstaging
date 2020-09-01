<?php

namespace App\Policies;

use Auth;
use App\Business;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any businesses.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // return false;
    }

    /**
     * Determine whether the user can view the business.
     *
     * @param  \App\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function view(User $user, Business $business)
    {
        if (!$user)
        {
            $user = Auth::user();
        }

        // owners can view their own business
        if ($business->owner && $user->id === $business->owner->id) {
            return true;
        }

        // advisors can view the businesses that they advise
        if ($business->license && $user->id === $business->license->advisor_id) {
            return true;
        }
        
        // advisors can view the businesses that they collaborate on
        if ($business->collaboration && $user->id === $business->collaboration->advisor_id) {
            // need to add expiry check
            return true;
        }

        // otherwise deny view
        return false;
    }

    /**
     * Determine whether the user can create businesses.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the business.
     *
     * @param  \App\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function update(User $user, Business $business)
    {
        // owners can update their own business
        if ($user->id === $business->owner->id) {
            return true;
        }

        // advisors can update the businesses that they advise
        if ($user->id === $business->license->advisor_id) {
            return true;
        }

        // advisors can update the businesses that they collaborate on
        if ($user->id === $business->collaboration->advisor_id) {
            // need to add expiry check
            return true;
        }

        // otherwise deny update
        return false;
    }

    /**
     * Determine whether the user can delete the business.
     *
     * @param  \App\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function delete(User $user, Business $business)
    {
        //
    }

    /**
     * Determine whether the user can restore the business.
     *
     * @param  \App\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function restore(User $user, Business $business)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the business.
     *
     * @param  \App\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function forceDelete(User $user, Business $business)
    {
        //
    }
}
