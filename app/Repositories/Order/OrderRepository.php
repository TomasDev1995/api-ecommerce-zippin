<?php

namespace App\Repositories\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Obtiene todas las órdenes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection
    {
        return Order::where('user_id', Auth::id())->get();
    }

    /**
     * Crea una nueva orden.
     *
     * @param int $userId
     * @param string $orderNumber
     * @param string $status
     * @param float $totalAmount
     * @param string $shippingAddress
     * @param string $billingAddress
     * @param string $paymentMethod
     * @param string $paymentStatus
     * @param string $orderDate
     * @param string|null $shippingDate
     * @param string|null $notes
     * @return \App\Models\Order
     */
    public function create(array $data): Order
    {
        $user = Auth::user();
        return Order::create([
            'user_id' => $user->id,
            'order_number' => $data['order_number'],
            'status' => $data['status'],
            'total_amount' => $data['total_amount'],
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'],
            'payment_method' => $data['payment_method'],
            'payment_status' => $data['payment_status'],
            'order_date' => $data['order_date'],
            'shipping_date' => $data['shipping_date'],
            'notes' => $data['notes'],
        ]);
    }

    /**
     * Encuentra una orden por su ID.
     *
     * @param int $id El ID de la orden.
     * @return \App\Models\Order|null
     */
    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }

    /**
     * Actualiza una orden existente.
     *
     * @param int $id El ID de la orden.
     * @param array $data Los datos actualizados de la orden.
     * @return \App\Models\Order|null
     */
    public function update(int $id, array $data): ?Order
    {
        $order = Order::find($id);

        if ($order) {
            $order->update($data);
        }

        return $order;
    }

    /**
     * Actualiza el estado de la orden.
     *
     * @param int $id El ID de la orden.
     * @param string $status El nuevo estado de la orden.
     * @return \App\Models\Order
     * @throws \Exception Si la orden no se encuentra.
     */
    public function setStatus(int $id, string $status): string
    {
        $order = Order::find($id);

        if (!$order) {
            throw new \Exception("Orden no encontrada.");
        }

        $order->status = $status;
        $order->save();

        return $order;
    }


    /**
     * Elimina una orden existente.
     *
     * @param int $id El ID de la orden.
     * @return bool
     */
    public function delete(int $id): bool
    {
        $order = Order::find($id);

        if ($order) {
            return $order->delete();
        }

        return false;
    }
}
