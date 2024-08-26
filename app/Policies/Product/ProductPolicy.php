<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determina si el usuario puede ver cualquier producto.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function viewAny(User $user)
    {
        return $user->role === 'admin' || $user->role === 'customer'
            ? Response::allow()
            : Response::deny('No tienes permiso para ver productos.');
    }

    /**
     * Determina si el usuario puede ver el producto.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response
     */
    public function view(User $user, Product $product)
    {
        return $user->role === 'admin' || $user->role === 'customer'
            ? Response::allow()
            : Response::deny('No tienes permiso para ver este producto.');
    }

    /**
     * Determina si el usuario puede crear productos.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function create(User $user)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Solo los administradores pueden crear productos.');
    }

    /**
     * Determina si el usuario puede actualizar el producto.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Product $product)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Solo los administradores pueden actualizar productos.');
    }

    /**
     * Determina si el usuario puede eliminar el producto.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Product $product)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Solo los administradores pueden eliminar productos.');
    }
}
