<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $userData): User
    {
        return User::create($userData);
    }

    public function findRoleById(int $roleId)
    {
        return Role::find($roleId);
    }
}
