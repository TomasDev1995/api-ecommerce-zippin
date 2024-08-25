<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Hash;

class PasswordService
{
    /**
     * Hashea la contraseña proporcionada.
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Verifica que la contraseña proporcionada coincida con el hash.
     *
     * @param string $password
     * @param string $hashedPassword
     * @return bool
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }
}
