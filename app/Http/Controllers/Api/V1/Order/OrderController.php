<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Muestra un pedido específico.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        // Verificar si el usuario está autorizado a ver el pedido
        $this->authorize('view', $order);

        return response()->json($order);
    }

    /**
     * Actualiza un pedido específico.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Order $order)
    {
        // Verificar si el usuario está autorizado a actualizar el pedido
        $this->authorize('update', $order);

        // Lógica para actualizar el pedido
    }

    /**
     * Cancela un pedido específico.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Order $order)
    {
        // Verificar si el usuario está autorizado a cancelar el pedido
        $this->authorize('cancel', $order);

        // Lógica para cancelar el pedido
    }

    /**
     * Elimina un pedido específico.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $order)
    {
        // Verificar si el usuario está autorizado a eliminar el pedido
        $this->authorize('delete', $order);

        // Lógica para eliminar el pedido
    }
}
