<?php

namespace App\Policies;

use Auth;
use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any businesses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // return false;
    }

    /**
     * Determine whether the user can view the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Business  $business
     * @return mixed
     */
    public function view(User $user, Business $business)
    {
        if (!$user)
        {
            $user = Auth::user();
        }

        return self::userHasBusinessAccess($user, $business);
    }

    /**
     * Determine whether the user can create businesses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can create businesses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function createBankAccount(User $user, Business $business)
    {
        return self::userHasBusinessAccess($user, $business);
    }

    /**
     * Determine whether the user can update the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Business  $business
     * @return mixed
     */
    public function update(User $user, Business $business)
    {
        return self::userHasBusinessAccess($user, $business);
    }

    /**
     * Determine whether the user can delete the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Business  $business
     * @return mixed
     */
    public function delete(User $user, Business $business)
    {
        return self::userHasBusinessAccess($user, $business);
    }

    /**
     * Determine whether the user can restore the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Business  $business
     * @return mixed
     */
    public function restore(User $user, Business $business)
    {
        return self::userHasBusinessAccess($user, $business);
    }

    /**
     * Determine whether the user can permanently delete the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Business  $business
     * @return mixed
     */
    public function forceDelete(User $user, Business $business)
    {
        //
    }

    /**
     * Returns true if the user is the owner of the account, an advisor to the
     * account or an advisor currently collaborating on the account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Business  $business
     * @return mixed
     */
    public function userHasBusinessAccess(User $user, Business $business)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // owners can access their own business
        if ($business->owner && $user->id === $business->owner->id) {
            return true;
        }

        // advisors can access the businesses that they advise
        // NOTE: do not use strict comparison === as it will return false.
        if ( $user->id == optional($business->advisor)->id ) {
            return true;
        }

        // advisors can access the businesses that they collaborate on
        // NOTE: do not use strict comparison === as it will return false.
        if ( $user->id == optional($business->collaboration)->advisor_id ) {
            // need to add expiry check
            return true;
        }

        // otherwise deny access
        return false;
    }
}
