<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Services\Order\OrderService;
use App\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = $this->orderService->getAll();

        if (empty($orders)) {
            return $this->error("No hay órdenes cargadas.", 404);
        }

        return $this->success($orders);
    }

    public function create(CreateOrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());

        if (isset($order['error'])) {
            return $this->error($order['error'], 500, $order);
        }

        return $this->success($order['order'], $order['message'], 201);
    }

    public function show(int $id)
    {
        $order = $this->orderService->findById($id);

        if (!$order) {
            return $this->error("Orden no encontrada.", 404);
        }

        return $this->success($order);
    }

    public function update(UpdateOrderRequest $request, int $id)
    {
        $order = $this->orderService->update($id, $request->validated());

        if (!$order) {
            return $this->error("No se pudo actualizar la orden.", 500);
        }

        return $this->success($order, "Orden actualizada exitosamente.");
    }

    public function cancel(UpdateOrderRequest $request, int $id)
    {
        if($request->input('status') != 'canceled'){
            return $this->error("No tiene permitida esta accion", 500);
        }
        
        $order = $this->orderService->setStatusOrder($id, $request->validated());

        if (!$order) {
            return $this->error("No se pudo cancelar la orden.", 500);
        }

        return $this->success($order, "Orden actualizada exitosamente.");
    }
}
