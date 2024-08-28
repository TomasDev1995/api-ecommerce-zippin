<?php

namespace App\Services\Order;

use App\Jobs\Order\NotifyOrderCreateJob;
use App\Jobs\Order\NotifyOrderStatusUpdateJob;
use App\Models\Order;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\OrderDetail\OrderDetailRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{

    protected $orderRepository;
    protected $orderDetailRepository;
    protected $invoiceRepository;
    protected $notifyJob;

    public function __construct(OrderRepositoryInterface $orderRepository, OrderDetailRepositoryInterface $orderDetailRepository, InvoiceRepositoryInterface $invoiceRepository, NotifyOrderCreateJob $notifyJob)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->notifyJob = $notifyJob;
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

    public function createOrder(array $data)
    {
        DB::beginTransaction();
        try {
            $order = $this->createOrderRecord($data);
            $this->createOrderDetails($data, $order);
            $this->createInvoice($data, $order);
            $this->notifyOrderCreation($order);
            DB::commit();
            
            $order->load('orderDetails.product', 'invoice');
            return [
                'order' => $order,
                'message' => 'Orden creada con éxito.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 500];
        }
    }

    /**
     * Crea el registro de la orden en la base de datos.
     *
     * @param array $data Los datos de la orden a crear.
     * @return \App\Models\Order
     */
    protected function createOrderRecord(array $data)
    {
        return $this->orderRepository->create($data);
    }

    /**
     * Crea los detalles de la orden en la base de datos.
     *
     * @param array $data Los datos de la orden.
     * @param \App\Models\Order $order La orden recién creada.
     * @return void
     */
    protected function createOrderDetails(array $data, Order $order)
    {
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
    }

    /**
     * Crea la factura para la orden en la base de datos.
     *
     * @param array $data Los datos de la orden.
     * @param \App\Models\Order $order La orden recién creada.
     * @return void
     */
    protected function createInvoice(array $data, Order $order)
    {
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
    }

    /**
     * Notifica la creación de la orden.
     *
     * @param \App\Models\Order $order La orden recién creada.
     * @return void
     */
    protected function notifyOrderCreation(Order $order)
    {
        $this->notifyJob::dispatchSync($order);
    }

    /**
     * Notifica la creación de la orden.
     *
     * @param \App\Models\Order $order La orden recién creada.
     * @return void
     */
    protected function notifyOrderChangeStatus(Order $order)
    {
        NotifyOrderStatusUpdateJob::dispatch($order);
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

    public function setStatusOrder(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->setStatus($id, $data['status']);
            $this->notifyOrderChangeStatus($order);

            DB::commit();
            return $order->toArray();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Error al actualizar el estado de la orden. Error: ' . $e->getMessage(), 500];
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
