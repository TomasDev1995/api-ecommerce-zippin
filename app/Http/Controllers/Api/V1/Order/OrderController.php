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
     * @OA\Get(
     *     path="/api/v1/orders",
     *     summary="List all orders",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No orders found"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/v1/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/CreateOrderRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating order"
     *     )
     * )
     */
    public function create(CreateOrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());
        
        if (isset($order['error'])) {
            return $this->error($order['error'], 500, $order);
        }

        return $this->success($order['order'], $order['message'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     summary="Get order details by ID",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/v1/orders/{id}",
     *     summary="Update an existing order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/UpdateOrderRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error updating order"
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, int $id)
    {
        $order = $this->orderService->update($id, $request->validated());

        if (!$order) {
            return $this->error("No se pudo actualizar la orden.", 500);
        }

        return $this->success($order, "Orden actualizada exitosamente.");
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/orders/{id}",
     *     summary="Delete an existing order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting order"
     *     )
     * )
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

