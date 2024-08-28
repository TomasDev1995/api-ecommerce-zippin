<?php

namespace Tests\Unit\Order;

use App\Jobs\Order\NotifyOrderCreateJob;
use App\Jobs\Order\NotifyOrderStatusUpdateJob;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\OrderDetail\OrderDetailRepositoryInterface;
use Tests\TestCase;
use App\Services\Order\OrderService;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

/**
 * Class OrderServiceTest
 *
 * @package Tests\Unit\Order
 */
class OrderServiceTest extends TestCase
{
    /** @var OrderService */
    protected $orderService;

    /** @var \Mockery\MockInterface|OrderRepositoryInterface */
    protected $orderRepositoryMock;

    /** @var \Mockery\MockInterface|OrderDetailRepositoryInterface */
    protected $orderDetailRepositoryMock;

    /** @var \Mockery\MockInterface|InvoiceRepositoryInterface */
    protected $invoiceRepositoryMock;

    /** @var \Mockery\MockInterface|NotifyOrderCreateJob */
    protected $notifyOrderCreateJobMock;

    /** @var \Mockery\MockInterface|NotifyOrderStatusUpdateJob */
    protected $notifyOrderStatusUpdateJobMock;

    /**
     * Set up the test environment.
     *
     * This method is called before each test is executed.
     * It initializes mocks and sets up the OrderService for testing
     * 
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mocks();
        $this->orderService = new OrderService(
            $this->orderRepositoryMock,
            $this->orderDetailRepositoryMock,
            $this->invoiceRepositoryMock,
            $this->notifyOrderCreateJobMock,
            $this->notifyOrderStatusUpdateJobMock
        );

        Queue::fake();
        Notification::fake();
    }

    /**
     * Configure the mocks for the repositories and jobs.
     *
     * This method sets up mock objects for repositories and jobs used in the OrderService.
     */
    protected function mocks()
    {
        $order = new Order();
        $order->id = 1;
        $order->user_id = 50;
        $order->order_number = 't-ORD12367';
        $order->status = 'pending';
        $order->total_amount = 100.00;
        $order->shipping_address = 'Don Bosco 64, Ciudad de Quilmes';
        $order->billing_address = 'Don Bosco 64, Ciudad de Quilmes';
        $order->payment_method = 'tarjeta_de_credito';
        $order->payment_status = 'pagado';
        $order->order_date = '2024-08-26';
        $order->shipping_date = '2024-08-30';
        $order->notes = 'Creacion de pedido de prueba numero 2';
        $order->billing_city = 'Ciudad de Quilmes';
        $order->billing_state = 'Buenos Aires';
        $order->billing_postal_code = '1878';
        $order->billing_country = 'Argentina';
        
        $orderDetail = new OrderDetail();
        $orderDetail->product_id = 1;
        $orderDetail->quantity = 1;
        $orderDetail->price = 50.00;
        
        $product = new Product();
        $product->id = 1;
        $product->name = 'Product 1';
        $product->price = 50.00;
        
        $orderDetail->setRelation('product', $product);
        $order->setRelation('orderDetails', collect([$orderDetail]));

        $invoice = new Invoice();
        $invoice->order_id = 1;
        $invoice->invoice_number = 'INV12345';
        $invoice->issued_at = now();
        $invoice->total_amount = 100.00;
        $invoice->billing_address = 'Don Bosco 64, Ciudad de Quilmes';
        $invoice->billing_city = 'Ciudad de Quilmes';
        $invoice->billing_state = 'Buenos Aires';
        $invoice->billing_postal_code = '1878';
        $invoice->billing_country = 'Argentina';
        $order->setRelation('invoice', $invoice);

        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $this->orderRepositoryMock->shouldReceive('load')
            ->with('orderDetails.product', 'invoice')
            ->andReturn($order);
        $this->orderRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($order);


        $this->orderDetailRepositoryMock = Mockery::mock(OrderDetailRepositoryInterface::class);
        $this->orderDetailRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($orderDetail);

        $this->invoiceRepositoryMock = Mockery::mock(InvoiceRepositoryInterface::class);
        $this->invoiceRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($invoice);

        $this->notifyOrderCreateJobMock = Mockery::mock(NotifyOrderCreateJob::class);
        $this->notifyOrderCreateJobMock->shouldReceive('dispatchSync')->once()->andReturnNull();
        $this->app->instance(NotifyOrderCreateJob::class, $this->notifyOrderCreateJobMock);

        $this->notifyOrderStatusUpdateJobMock = Mockery::mock(NotifyOrderStatusUpdateJob::class);
        $this->app->instance(NotifyOrderStatusUpdateJob::class, $this->notifyOrderStatusUpdateJobMock);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();
    }

    /**
     * Test the creation of an order.
     *
     * This test verifies that an order is correctly created by the OrderService.
     *
     * @return void
     */
    public function testCreateOrder()
    {
        $data = $this->getOrderData();
        $order = $this->orderService->createOrder($data);

        $this->assertNotNull($order);
        $this->assertEquals($data['user_id'], $order['order']->user_id);
        $this->assertEquals($data['total_amount'], $order['order']->total_amount);
    }

    /**
     * Get the data for creating an order.
     *
     * @return array
     */
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

    /**
     * Clean up after the test.
     *
     * This method is called after each test is executed.
     * It closes Mockery and calls the parent tearDown method.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
