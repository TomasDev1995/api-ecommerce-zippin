<?php

namespace App\Repositories\User\Admin;

use App\Models\User;

interface AdminRepositoryInterface
{
    public function create(?array $data);
    public function findByEmail(?string $email);
    public function verifyPassword(string $password, User $user);
}
