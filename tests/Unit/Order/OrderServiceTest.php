<?php 
namespace Tests\Unit\Order;

use App\Jobs\Order\NotifyOrderCreateJob;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\OrderDetail\OrderDetailRepositoryInterface;
use Tests\TestCase;
use App\Services\Order\OrderService;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

class OrderServiceTest extends TestCase
{
    protected $orderService;
    protected $orderRepositoryMock;
    protected $orderDetailRepositoryMock;
    protected $invoiceRepositoryMock;
    protected $notifyOrderCreateJob;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mocks();
        $this->orderService = new OrderService(
            $this->orderRepositoryMock,
            $this->orderDetailRepositoryMock,
            $this->invoiceRepositoryMock,
            $this->notifyOrderCreateJob
        );
    }

    protected function mocks()
    {
        // Mock del repositorio de órdenes
        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $order = Mockery::mock(Order::class);
        $order->shouldReceive('load')->with('orderDetails.product', 'invoice')->andReturnSelf();
        $this->orderRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($order);

        // Mock del repositorio de detalles de órdenes
        $this->orderDetailRepositoryMock = Mockery::mock(OrderDetailRepositoryInterface::class);
        $this->orderDetailRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn(new OrderDetail());

        // Mock del repositorio de facturas
        $this->invoiceRepositoryMock = Mockery::mock(InvoiceRepositoryInterface::class);
        $this->invoiceRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn(new Invoice());

        $this->notifyOrderCreateJob = Mockery::mock(NotifyOrderCreateJob::class);
        $this->notifyOrderCreateJob->shouldReceive('dispatchSync')
            ->once()
            ->andReturn(null);

        // Mock de transacciones de base de datos
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();
    }

    public function testCreateOrder()
    {
        $data = $this->getOrderData();
        $order = $this->orderService->createOrder($data);
        $this->notifyOrderCreateJob->shouldHaveReceived('dispatchSync')
        ->once();

        $this->assertNotNull($order);
        $this->assertEquals(1, $order['order']->id);
        $this->assertEquals($data['user_id'], $order['order']->user_id);
        $this->assertEquals($data['total_amount'], $order['order']->total_amount);
    }

    protected function getOrderData(): array
    {
        return [
            'user_id' => 50,
            'order_number' => 't-ORD12367',
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
