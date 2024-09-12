<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $userData): User;
    public function findRoleById(int $roleId);
}
