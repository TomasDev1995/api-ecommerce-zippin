<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view their own orders.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function viewOwnOrders(User $user, Order $order)
    {
        return $user->hasRole('customer') && $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can create orders.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function createOrders(User $user)
    {
        return $user->hasRole('customer');
    }
}
