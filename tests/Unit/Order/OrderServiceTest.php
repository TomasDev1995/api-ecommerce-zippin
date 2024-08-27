<?php

namespace Tests\Unit\Order;

use App\Models\Order;
use Tests\TestCase;
use App\Services\Order\OrderService;
use Carbon\Carbon;
use Mockery;
use Illuminate\Support\Facades\DB;

class OrderServiceTest extends TestCase
{
    protected $orderService;
    protected $orderRepositoryMock;
    protected $orderDetailRepositoryMock;
    protected $productRepositoryMock;
    protected $invoiceRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepositories();
        $this->orderService = new OrderService(
            $this->orderRepositoryMock,
            $this->orderDetailRepositoryMock,
            $this->productRepositoryMock,
            $this->invoiceRepositoryMock
        );
    }

    protected function mockRepositories()
    {
        // Mock del repositorio de órdenes
        $this->orderRepositoryMock = Mockery::mock('App\Repositories\Order\OrderRepositoryInterface');
        $this->orderRepositoryMock->shouldReceive('create')
            ->andReturn($this->createOrderModel());

        // Mock del repositorio de detalles de órdenes
        $this->orderDetailRepositoryMock = Mockery::mock('App\Repositories\OrderDetail\OrderDetailRepositoryInterface');
        $this->orderDetailRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($this->createOrderDetail());

        // Mock del repositorio de productos
        $this->productRepositoryMock = Mockery::mock('App\Repositories\Product\ProductRepositoryInterface');

        // Mock del repositorio de facturas
        $this->invoiceRepositoryMock = Mockery::mock('App\Repositories\Invoice\InvoiceRepositoryInterface');
        $this->invoiceRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($this->createInvoice());

        // Usar directamente el Facade para simular los métodos
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();
    }

    protected function createOrderModel(): Order
    {
        return new Order([
            'id' => 14,
            'user_id' => 4,
            'order_number' => 'ORD12360-test-unit',
            'status' => 'pending',
            'total_amount' => 100.00,
            'shipping_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'billing_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'payment_method' => 'tarjeta_de_credito',
            'payment_status' => 'pagado',
            'order_date' => Carbon::now(),
            'shipping_date' => Carbon::now()->addDays(3),
            'notes' => 'Creacion de pedido de prueba numero 2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createOrderDetail()
    {
        return (object)[
            'id' => 1,
            'order_id' => 14,
            'product_id' => 1,
            'quantity' => 2,
            'price' => 50.00
        ];
    }

    protected function createInvoice()
    {
        return (object)[
            'order_id' => 14,
            'invoice_number' => 'INV-1001',
            'total_amount' => 100.00,
            'billing_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'billing_city' => 'Ciudad de Quilmes',
            'billing_state' => 'Buenos Aires',
            'billing_postal_code' => '1878',
            'billing_country' => 'Argentina',
        ];
    }

    public function testCreateOrder()
    {
        $data = $this->getOrderData();

        $order = $this->orderService->createOrder($data);

        $this->assertOrderCreated($order, $data);
    }

    protected function getOrderData(): array
    {
        return [
            'user_id' => 4,
            'order_number' => 'ORD12350',
            'status' => 'pending',
            'total_amount' => 100.00,
            'shipping_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'billing_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'billing_city' => 'Ciudad de Quilmes',
            'billing_state' => 'Buenos Aires',
            'billing_postal_code' => '1878',
            'billing_country' => 'Argentina',
            'payment_method' => 'tarjeta_de_credito',
            'payment_status' => 'pagado',
            'order_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'shipping_date' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
            'notes' => 'Creacion de pedido de prueba numero 2',
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 1,
                    'price' => 50.00,
                    'total' => 50.00 
                ],
                [
                    'product_id' => 2,
                    'quantity' => 1,
                    'price' => 50.00,
                    'total' => 50.00 
                ]
            ],
        ];
    }

    protected function assertOrderCreated($order, array $data)
    {
        $this->assertNotNull($order);
        $this->assertEquals(1, $order['order']->id);
        $this->assertEquals($data['user_id'], $order['order']->user_id);
        $this->assertEquals($data['total_amount'], $order['order']->total_amount);
    }
}
