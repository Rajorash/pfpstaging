<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\Business;
use App\Models\User;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any bank accounts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(Business $business)
    {
        if (!$user) {
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
     * Determine whether the user can view the bank account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BankAccount  $bankAccount
     * @return mixed
     */
    public function view(User $user, BankAccount $bankAccount)
    {
        if (!$user) {
            $user = Auth::user();
        }

        return self::userHasBusinessAccess($user, $bankAccount->business);
    }

    /**
     * Determine whether the user can create bank accounts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the bank account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BankAccount  $bankAccount
     * @return mixed
     */
    public function update(User $user, BankAccount $bankAccount)
    {
        return self::userHasBusinessAccess($user, $bankAccount->business);
    }

    /**
     * Determine whether the user can delete the bank account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BankAccount  $bankAccount
     * @return mixed
     */
    public function delete(User $user, BankAccount $bankAccount)
    {
        return self::userHasBusinessAccess($user, $bankAccount->business);
    }

    /**
     * Determine whether the user can restore the bank account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BankAccount  $bankAccount
     * @return mixed
     */
    public function restore(User $user, BankAccount $bankAccount)
    {
        return self::userHasBusinessAccess($user, $bankAccount->business);
    }

    /**
     * Determine whether the user can permanently delete the bank account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BankAccount  $bankAccount
     * @return mixed
     */
    public function forceDelete(User $user, BankAccount $bankAccount)
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
}
