<?php

namespace App\Repositories\User\Customer;

use App\Models\User;
use App\Services\Security\PasswordService;

class CustomerRepository {

    protected $passwordService;
    
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * Crea un nuevo usuario cliente.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->passwordService->hashPassword($data['password']),
        ]);
    }

     /**
     * Busca un usuario por su email.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
     public function findByEmail(?string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Verifica si la contraseña proporcionada coincide con el hash almacenado.
     *
     * @param string $password
     * @param \App\Models\User $user
     * @return bool
     */
    public function verifyPassword(string $password, User $user): bool
    {
        return $this->passwordService->verifyPassword($password, $user->password);
    }
}