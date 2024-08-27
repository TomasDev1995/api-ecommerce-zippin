<?php

namespace Tests\Unit\Order;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderDetail;
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

    /**
     * Configura el entorno de prueba antes de cada prueba.
     * Aquí se inicializan los mocks y el servicio de órdenes.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepositories();
        // Inicializa el servicio de órdenes con los mocks
        $this->orderService = new OrderService(
            $this->orderRepositoryMock,
            $this->orderDetailRepositoryMock,
            $this->productRepositoryMock,
            $this->invoiceRepositoryMock
        );
    }

    /**
     * Prueba la función de creación de una orden en el servicio de órdenes.
     * Se verifica si la orden se crea correctamente.
     */
    public function testCreateOrder()
    {
        // Datos para crear una orden
        $data = $this->getOrderData();

        // Llama al método createOrder del servicio
        $order = $this->orderService->createOrder($data);
        
        // Verifica que la orden fue creada correctamente
        $this->assertOrderCreated($order, $data);
    }

    /**
     * Configura los mocks de los repositorios utilizados en las pruebas.
     * Los métodos esperados y los valores de retorno son definidos aquí.
     */
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
            ->andReturn($this->createOrderDetailModel());

        // Mock del repositorio de productos
        $this->productRepositoryMock = Mockery::mock('App\Repositories\Product\ProductRepositoryInterface');

        // Mock del repositorio de facturas
        $this->invoiceRepositoryMock = Mockery::mock('App\Repositories\Invoice\InvoiceRepositoryInterface');
        $this->invoiceRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($this->createInvoiceModel());

        // Mock de transacciones de base de datos
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();
    }

    /**
     * Crea un modelo de orden para pruebas.
     * 
     * @return Order
     */
    protected function createOrderModel(): Order
    {
        return new Order([
            'id' => 14,
            'user_id' => 4,
            'order_number' => 'ORD12356',
            'status' => 'pending',
            'total_amount' => 100.00,
            'shipping_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'billing_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'payment_method' => 'tarjeta_de_credito',
            'payment_status' => 'pagado',
            'order_date' => Carbon::parse('2024-08-26'),
            'shipping_date' => Carbon::parse('2024-08-30'),
            'notes' => 'Creacion de pedido de prueba numero 2',
            'billing_city' => 'Ciudad de Quilmes',
            'billing_state' => 'Buenos Aires',
            'billing_postal_code' => '1878',
            'billing_country' => 'Argentina',
        ]);
    }

    /**
     * Crea un modelo de detalle de orden para pruebas.
     * 
     * @return OrderDetail
     */
    protected function createOrderDetailModel(): OrderDetail
    {
        return new OrderDetail([
            'id' => 1,
            'order_id' => 14,
            'product_id' => 1,
            'quantity' => 2,
            'price' => 50.00
        ]);
    }

    /**
     * Crea un modelo de factura para pruebas.
     * 
     * @return Invoice
     */
    protected function createInvoiceModel(): Invoice
    {
        return new Invoice([
            'order_id' => 14,
            'invoice_number' => 'INV-1001',
            'total_amount' => 100.00,
            'billing_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'billing_city' => 'Ciudad de Quilmes',
            'billing_state' => 'Buenos Aires',
            'billing_postal_code' => '1878',
            'billing_country' => 'Argentina',
        ]);
    }

    /**
     * Proporciona datos de prueba para crear una orden.
     * 
     * @return array
     */
    protected function getOrderData(): array
    {
        return [
            'order_number' => 'ORD12356',
            'status' => 'pending',
            'total_amount' => 100.00,
            'shipping_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'billing_address' => 'Don Bosco 64, Ciudad de Quilmes',
            'payment_method' => 'tarjeta_de_credito',
            'payment_status' => 'pagado',
            'order_date' => '2024-08-26',
            'shipping_date' => '2024-08-30',
            'notes' => 'Creacion de pedido de prueba numero 2',
            'billing_city' => 'Ciudad de Quilmes',
            'billing_state' => 'Buenos Aires',
            'billing_postal_code' => '1878',
            'billing_country' => 'Argentina',
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 1,
                    'price' => 50.00
                ]
            ]
        ];
    }

    /**
     * Verifica que la orden haya sido creada correctamente.
     * 
     * @param $order
     * @param array $data
     */
    protected function assertOrderCreated($order, array $data)
    {
        $this->assertNotNull($order);
        $this->assertEquals(1, $order['order']->id);
        $this->assertEquals($data['user_id'], $order['order']->user_id);
        $this->assertEquals($data['total_amount'], $order['order']->total_amount);
    }
}
