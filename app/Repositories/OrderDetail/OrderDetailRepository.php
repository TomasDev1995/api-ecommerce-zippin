<?php

namespace App\Repositories\OrderDetail;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Collection;

class OrderDetailRepository implements OrderDetailRepositoryInterface
{
    /**
     * Crea una nueva orden.
     *
     * @param array $data Los datos de la orden a crear.
     * @return \App\Models\Order
     */
    public function create(array $data)
    {
        //dd(gettype($data['total']), $data['total']);
        return OrderDetail::create([
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'total' => $data['total']
        ]);
    }
}
