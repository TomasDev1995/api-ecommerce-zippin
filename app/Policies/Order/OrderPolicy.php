<?php
namespace App\Policies\Order;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determina si el usuario puede ver el pedido.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Order $order
     * @return bool
     */
    public function view(User $user, Order $order)
    {
        // Los administradores pueden ver cualquier pedido
        // Los clientes solo pueden ver sus propios pedidos
        return $user->role === 'admin' || $user->id === $order->user_id;
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
        // Los administradores pueden actualizar cualquier pedido
        // Los clientes solo pueden actualizar sus propios pedidos si el estado es "pendiente"
        return $user->role === 'admin' || ($user->id === $order->user_id && $order->status === 'pending');
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
        // Los administradores pueden cancelar cualquier pedido
        // Los clientes solo pueden cancelar sus propios pedidos si el estado es "pendiente"
        return $user->role === 'admin' || ($user->id === $order->user_id && $order->status === 'pending');
    }

    /**
     * Determina si el usuario puede eliminar el pedido.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Order $order
     * @return bool
     */
    public function delete(User $user, Order $order)
    {
        // Solo los administradores pueden eliminar pedidos
        return $user->role === 'admin';
    }
}
