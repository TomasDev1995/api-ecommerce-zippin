<?php

namespace App\Repositories\User\Customer;

use App\Models\User;

interface CustomerRepositoryInterface
{
    public function create(?array $data);
    public function findByEmail(?string $email);
    public function verifyPassword(string $password, User $user);
}
