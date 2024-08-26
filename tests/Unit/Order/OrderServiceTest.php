<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderServiceTest extends TestCase
{
    protected $orderService;
    protected $orderRepositoryMock;
    protected $dbMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mock del repositorio de órdenes
        $this->orderRepositoryMock = Mockery::mock('App\Repositories\Order\OrderRepositoryInterface');
        $this->orderRepositoryMock->shouldReceive('create')->andReturn((object)['id' => 1, 'user_id' => 1, 'total_amount' => 100.00, 'status' => 'pending']);

        // Mock de DB facade
        $this->dbMock = Mockery::mock('Illuminate\Database\DatabaseManager');
        $this->dbMock->shouldReceive('beginTransaction')->once()->andReturn(null);
        $this->dbMock->shouldReceive('commit')->once()->andReturn(null);
        $this->dbMock->shouldReceive('rollBack')->once()->andReturn(null);

        DB::shouldReceive('getFacadeRoot')->andReturn($this->dbMock);

        $this->orderService = new OrderService($this->orderRepositoryMock);
    }

    public function testCreateOrder()
    {
        $data = [
            'user_id' => 1,
            'total_amount' => 100.00,
            'status' => 'pending',
        ];

        $order = $this->orderService->create($data);

        $this->assertNotNull($order);
        $this->assertEquals(1, $order->id);
        $this->assertEquals($data['user_id'], $order->user_id);
        $this->assertEquals($data['total_amount'], $order->total_amount);
        $this->assertEquals($data['status'], $order->status);
    }
}
