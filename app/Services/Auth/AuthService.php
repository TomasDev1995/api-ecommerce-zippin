<?php

namespace App\Services\Auth;

use App\Jobs\Auth\Customer\SendWelcomeEmail;
use App\Repositories\User\Customer\CustomerRepository;
use App\Repositories\User\Admin\AdminRepository;

class AuthService {

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var AdminRepository
     */
    protected $adminRepository;

    /**
     * Crea una nueva instancia de AuthService.
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository, AdminRepository $adminRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->adminRepository = $adminRepository;
    }

    /**
     * Registra un nuevo cliente en el sistema.
     *
     * @param array|null $data Los datos del cliente a registrar.
     * @return \App\Models\User El usuario creado.
     */
    public function customerRegister(?array $data)
    {
        $user = $this->customerRepository->create($data);
        SendWelcomeEmail::dispatch($user);
        return $user;
    }

    /**
     * Inicia sesión de un cliente.
     *
     * Este método verifica la existencia del usuario por su email y valida la contraseña.
     * Si el usuario y la contraseña son válidos, genera un token de acceso.
     *
     * @param array|null $data Los datos de inicio de sesión del cliente.
     * @return array Una respuesta que indica el éxito o fracaso del inicio de sesión y los datos del token.
     */
    public function customerLogin(?array $data)
    {
        // Buscar el usuario por su email
        if (!$user = $this->customerRepository->findByEmail($data['email'])) {
            return [
                'success' => false,
                'message' => 'El usuario no existe.',
                'data' => null
            ];
        }

        // Verificar la contraseña
        if (!$this->customerRepository->verifyPassword($data['password'], $user)) {
            return [
                'success' => false,
                'message' => 'Contraseña incorrecta.',
                'data' => null
            ];
        }

        $userToken = $user->tokens()->first();
        if ($userToken) {
            $userToken->delete();
        }

        // Generar el token de acceso
        if (!$token = $user->createToken("Personal Access Token")) {
            return [
                'success' => false,
                'message' => 'Error al generar el token de acceso.',
                'data' => null
            ];
        }

        return [
            'success' => true,
            'message' => 'Cliente autenticado!',
            'data' => [
                'accessToken' => [
                    'token' => $token->plainTextToken,
                    'expires_at' => $token->accessToken->expires_at
                ]
            ]
        ];
    }

    /**
     * Registra un nuevo administrador en el sistema.
     *
     * @param array $data Los datos del administrador a registrar.
     * @return \App\Models\User El usuario creado.
     */
    public function adminRegister($data)
    {
        return $this->adminRepository->create($data);
    }

    /**
     * Inicia sesión de un administrador.
     *
     * @param array $data Los datos de inicio de sesión del administrador.
     * @return \App\Models\User El usuario autenticado.
     */
    public function adminLogin($data)
    {
        // Buscar el usuario por su email
        if (!$user = $this->adminRepository->findByEmail($data['email'])) {
            return [
                'success' => false,
                'message' => 'El usuario no existe.',
                'data' => null
            ];
        }

        // Verificar la contraseña
        if (!$this->adminRepository->verifyPassword($data['password'], $user)) {
            return [
                'success' => false,
                'message' => 'Contraseña incorrecta.',
                'data' => null
            ];
        }

        // Generar el token de acceso
        if (!$token = $user->createToken("Personal Access Token")) {
            return [
                'success' => false,
                'message' => 'Error al generar el token de acceso.',
                'data' => null
            ];
        }

        return [
            'success' => true,
            'message' => 'Admin autenticado!',
            'data' => [
                'accessToken' => [
                    'token' => $token->plainTextToken,
                    'expires_at' => $token->accessToken->expires_at
                ]
            ]
        ];
    }

}
