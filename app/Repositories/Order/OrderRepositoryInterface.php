<?php

namespace App\Repositories\Order;

use PhpParser\Node\Expr\Cast\String_;

interface OrderRepositoryInterface
{
    /**
     * Obtiene todas las órdenes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Crea una nueva orden.
     *
     * @param array $data Los datos de la orden a crear.
     * @return \App\Models\Order
     */
    public function create(array $data);

    /**
     * Encuentra una orden por su ID.
     *
     * @param int $id El ID de la orden.
     * @return \App\Models\Order|null
     */
    public function findById(int $id);

    /**
     * Actualiza una orden existente.
     *
     * @param int $id El ID de la orden.
     * @param array $data Los datos actualizados de la orden.
     * @return \App\Models\Order|null
     */
    public function update(int $id, array $data);

    /**
     * Actualiza el estado una orden existente.
     *
     * @param int $id El ID de la orden.
     * @param array $status estado a setear.
     * @return \App\Models\Order|null
     */
    public function setStatus(int $id, string $status);

    /**
     * Elimina una orden existente.
     *
     * @param int $id El ID de la orden.
     * @return bool
     */
    public function delete(int $id);
}
