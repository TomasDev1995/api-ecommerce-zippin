<?php
namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Obtiene todas las órdenes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Order::all();
    }

    /**
     * Crea una nueva orden.
     *
     * @param array $data Los datos de la orden a crear.
     * @return \App\Models\Order|null
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $order = Order::create($data);
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error message if needed
            return null;
        }
    }

    /**
     * Encuentra una orden por su ID.
     *
     * @param int $id El ID de la orden.
     * @return \App\Models\Order|null
     */
    public function findById(int $id)
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
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($id);

            if (!$order) {
                return null;
            }

            $order->update($data);
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error message if needed
            return null;
        }
    }

    /**
     * Elimina una orden existente.
     *
     * @param int $id El ID de la orden.
     * @return bool
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($id);

            if (!$order) {
                return false;
            }

            $order->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error message if needed
            return false;
        }
    }
}
