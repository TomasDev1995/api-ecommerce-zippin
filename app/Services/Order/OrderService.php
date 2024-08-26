<?php

namespace App\Services\Order;

use App\Jobs\Order\NotifyOrderCreate;
use App\Jobs\Order\NotifyOrderStatusUpdate;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\OrderDetail\OrderDetailRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{

    protected $orderRepository;
    protected $productRepository;
    protected $orderDetailRepository;
    protected $invoiceRepository;

    public function __construct(OrderRepositoryInterface $orderRepository, OrderDetailRepositoryInterface $orderDetailRepository, ProductRepositoryInterface $productRepository, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->productRepository = $productRepository;
        $this->invoiceRepository = $invoiceRepository;
    }
    
    /**
     * Obtiene todas las órdenes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {   
        $orders = $this->orderRepository->getAll();
        $orders->load('orderDetails.product');
        return $orders->toArray();
    }

    /**
     * Crea una nueva orden.
     *
     * @param array $data Los datos de la orden a crear.
     * @return \App\Models\Order|null
     */
    public function createOrder(array $data)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->create($data);
            
            foreach ($data["products"] as $product) {
                $productData = [
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['quantity'] * $product['price'],
                ];
                $this->orderDetailRepository->create($productData);

            }

            $invoiceData = [
                'order_id' => $order->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'issued_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'total_amount' => $order->total_amount,
                'billing_address' => $data['billing_address'],
                'billing_city' => $data['billing_city'],
                'billing_state' => $data['billing_state'],
                'billing_postal_code' => $data['billing_postal_code'],
                'billing_country' => $data['billing_country'],
            ];
            
            $this->invoiceRepository->create($invoiceData);

            NotifyOrderCreate::dispatch($order);
            
            DB::commit();

            $order->load('orderDetails.product','invoice');
            
            return [
                'order' => $order,
                'message' => 'Orden creada con éxito.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Error al crear la orden. Error: '.$e->getMessage(), 500];
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
        $order = $this->orderRepository->findById($id);
        $order->load('orderDetails.product');
        return $order->toArray();
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
            $order = $this->orderRepository->update($id, $data);
            NotifyOrderStatusUpdate::dispatch($order);

            DB::commit();
            return $order->toArray();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Error al actualizar el estado de la orden. Error: '.$e->getMessage(), 500];
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

    /**
     * Genera un número de factura único.
     *
     * @return string
     */
    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . strtoupper(uniqid());
    }
}
