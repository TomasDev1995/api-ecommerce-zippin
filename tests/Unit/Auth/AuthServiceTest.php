<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Illuminate\Support\Facades\DB;
use App\Repositories\User\Admin\AdminRepositoryInterface;
use App\Repositories\User\Customer\CustomerRepositoryInterface;

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
        $this->authService = new AuthService($this->customerRepositoryMock, $this->adminRepositoryMock);
    }

    /**
     * Configura los mocks de los repositorios utilizados en las pruebas.
     * Los métodos esperados y los valores de retorno son definidos aquí.
     */
    protected function mockRepositories()
    {
        $this->customerRepositoryMock = Mockery::mock(CustomerRepositoryInterface::class);
        $this->customerRepositoryMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($userData) {
                return isset($userData['name'], $userData['email'], $userData['password']);
            }))
            ->andReturn($this->createUserModel());

        $this->adminRepositoryMock = Mockery::mock(AdminRepositoryInterface::class);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();
    }

    /**
     * Crea un modelo de usuario para pruebas.
     * 
     * @return User
     */
    protected function createUserModel()
    {
        $user = Mockery::mock('App\Models\User')->makePartial();
    
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('John Doe');
        $user->shouldReceive('getAttribute')->with('email')->andReturn('john.doe@example.com');
        $user->shouldReceive('getAttribute')->with('password')->andReturn(Hash::make('password123'));
        $user->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $user->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    
        $user->shouldReceive('getQueueableId')->andReturn(1);
        $user->shouldReceive('getQueueableRelations')->andReturn([]);
        $user->shouldReceive('getQueueableConnection')->andReturn(null);
    
        $user->shouldReceive('getAttributes')->andReturn([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
        return $user;
    }

    /**
     * Prueba el registro de un nuevo usuario a través del servicio de autenticación.
     * 
     * @return void
     */
    public function testRegisterUser()
    {
        $data = $this->getUserData();
        $user = $this->authService->customerRegister($data);
        $this->assertUserRegistered($user, $data);
    }

    /**
     * Proporciona datos de prueba para registrar un nuevo usuario.
     * 
     * @return array
     */
    protected function getUserData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];
    }

    /**
     * Verifica que el usuario haya sido registrado correctamente.
     * 
     * @param User $user
     * @param array $data
     * @return void
     */
    protected function assertUserRegistered(User $user, array $data)
    {
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertTrue(Hash::check($data['password'], $user->password)); 
    }
}
