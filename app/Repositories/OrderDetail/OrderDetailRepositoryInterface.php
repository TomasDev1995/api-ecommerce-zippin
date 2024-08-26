<?php

namespace App\Repositories\OrderDetail;

interface OrderDetailRepositoryInterface
{
    /**
     * Crea una nueva orden.
     *
     * @param array $data Los datos de la orden a crear.
     * @return \App\Models\Order
     */
    public function create(array $data);
}
