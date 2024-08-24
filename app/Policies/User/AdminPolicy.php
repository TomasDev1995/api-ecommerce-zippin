<?php

namespace App\Policies\User;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage users.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function manageUsers(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage products.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function manageProducts(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage orders.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function manageOrders(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage invoices.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function manageInvoices(User $user)
    {
        return $user->hasRole('admin');
    }
}
