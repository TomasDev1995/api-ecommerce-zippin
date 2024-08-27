<?php 

namespace App\Policies\Order;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{

        /**
     * Determina si el usuario puede crear un pedido.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user)
    {
        // Solo los usuarios con el rol 'customer' pueden crear un pedido
        return $user->role === 'customer';
    }
    
    /**
     * Determina si el usuario puede actualizar el pedido.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Order $order
     * @return bool
     */
    public function update(User $user, Order $order)
    {
        // Solo el administrador puede actualizar el estado a cualquier otro estado.
        return $user->role === 'admin';
    }

    /**
     * Determina si el usuario puede cancelar el pedido.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Order $order
     * @return bool
     */
    public function cancel(User $user, Order $order)
    {
        // El cliente puede cancelar su propio pedido si está pendiente.
        return $user->id === $order->user_id && $order->status === 'pending';
    }
}
