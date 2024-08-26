<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Illuminate\Support\Facades\DB;
use App\Repositories\User\Customer\CustomerRepository;
use App\Repositories\User\Admin\AdminRepository;

class AuthServiceTest extends TestCase
{
    protected $authService;
    protected $customerRepositoryMock;
    protected $adminRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepositories();
        $this->authService = new AuthService($this->customerRepositoryMock, $this->adminRepositoryMock);
    }

    protected function mockRepositories()
    {
        // Mock del repositorio de clientes
        $this->customerRepositoryMock = Mockery::mock(CustomerRepository::class);
        $this->customerRepositoryMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($userData) {
                return isset($userData['name'], $userData['email'], $userData['password']);
            }))
            ->andReturn($this->createUserModel());

        // Mock del repositorio de administradores (puedes personalizar según sea necesario)
        $this->adminRepositoryMock = Mockery::mock(AdminRepository::class);

        // Usar directamente el Facade para simular los métodos
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();
    }

    protected function createUserModel()
    {
        // Crear un mock del modelo User
        $user = Mockery::mock(User::class)->makePartial(); // Usa makePartial para permitir el uso de métodos reales
    
        // Configurar el mock para devolver valores específicos para atributos
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('John Doe');
        $user->shouldReceive('getAttribute')->with('email')->andReturn('john.doe@example.com');
        $user->shouldReceive('getAttribute')->with('password')->andReturn(Hash::make('password123'));
        $user->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $user->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    
        // Configurar el mock para métodos relacionados con la cola
        $user->shouldReceive('getQueueableId')->andReturn(1);
        $user->shouldReceive('getQueueableRelations')->andReturn([]);
        $user->shouldReceive('getQueueableConnection')->andReturn(null);
    
        // Configurar el mock para cualquier otro método requerido
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
    
    

    public function testRegisterUser()
    {
        $data = $this->getUserData();

        $user = $this->authService->customerRegister($data);

        $this->assertUserRegistered($user, $data);
    }

    protected function getUserData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123', // La contraseña original
        ];
    }

    protected function assertUserRegistered(User $user, array $data)
    {
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertTrue(Hash::check($data['password'], $user->password)); // Verifica que la contraseña esté correctamente hasheada
    }
}
