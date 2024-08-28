<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Repositories\User\Admin\AdminRepositoryInterface;
use App\Repositories\User\Customer\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

class AuthServiceTest extends TestCase
{
    protected $authService;
    protected $customerRepositoryMock;
    protected $adminRepositoryMock;

    /**
     * Configura el entorno de prueba antes de cada prueba.
     * Aquí se inicializan los mocks y el servicio de autenticación.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepositories();
        $this->authService = new AuthService(
            $this->customerRepositoryMock,
            $this->adminRepositoryMock
        );
        
        Queue::fake();
        Notification::fake();
    }

    /**
     * Configura los mocks de los repositorios utilizados en las pruebas.
     * Los métodos esperados y los valores de retorno son definidos aquí.
     */
    protected function mockRepositories()
    {
        $customer = new User();
        $customer->id = 1;
        $customer->name = "Juan Alvarez";
        $customer->email = "Juan@gmail.com";
        $customer->password = "zippin123";
        $customer->role = "customer";

        $this->customerRepositoryMock = Mockery::mock(CustomerRepositoryInterface::class);
        $this->customerRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($customer);

        $this->adminRepositoryMock = Mockery::mock(AdminRepositoryInterface::class);
    }

    /**
     * Prueba el registro de un nuevo usuario a través del servicio de autenticación.
     * 
     * @return void
     */
    public function testCustomerRegister()
    {
        $data = $this->getUserData();
        $user = $this->authService->customerRegister($data);
        
        $this->assertNotNull($user, 'El usuario no debería ser nulo.');
        $this->assertEquals(1, $user->id, 'El ID del usuario debería ser 1.');
        $this->assertEquals($data['name'], $user->name, 'El nombre del usuario no coincide.');
        $this->assertEquals($data['email'], $user->email, 'El correo electrónico del usuario no coincide.');
        $this->assertTrue(Hash::check($data['password'], $user->password), 'La contraseña no coincide.');
    }

    /**
     * Proporciona datos de prueba para registrar un nuevo usuario.
     * 
     * @return array
     */
    protected function getUserData(): array
    {
        return [
            'name' => 'Juan Alvarez',
            'email' => 'Juan@gmail.com',
            'password' => 'zippin123',
            'role' => 'customer'
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
