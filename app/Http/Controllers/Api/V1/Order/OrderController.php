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

    /**
     * Muestra una lista de órdenes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = $this->orderService->getAll();

        if (empty($orders)) {
            return $this->error("No hay órdenes cargadas.", 404);
        }

        return $this->success($orders);
    }

    /**
     * Crea una nueva orden.
     *
     * @param  \App\Http\Requests\Order\CreateOrderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOrderRequest $request)
    {
        $order = $this->orderService->create($request->validated());

        if (!$order) {
            return $this->error("No se pudo crear la orden.", 500);
        }

        return $this->success($order, "Orden creada exitosamente.", 201);
    }

    /**
     * Muestra los detalles de una orden específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $order = $this->orderService->findById($id);

        if (!$order) {
            return $this->error("Orden no encontrada.", 404);
        }

        return $this->success($order);
    }

    /**
     * Actualiza una orden existente.
     *
     * @param  \App\Http\Requests\Order\UpdateOrderRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOrderRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $order = $this->orderService->update($id, $validatedData);

        if (!$order) {
            return $this->error("No se pudo actualizar la orden.", 500);
        }

        return $this->success($order, "Orden actualizada exitosamente.");
    }

    /**
     * Elimina una orden existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $result = $this->orderService->delete($id);

        if (!$result) {
            return $this->error("No se pudo eliminar la orden.", 500);
        }

        return $this->success(null, "Orden eliminada exitosamente.");
    }
}

