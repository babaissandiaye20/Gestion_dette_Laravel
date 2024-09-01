<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut créer d'autres utilisateurs.
     */
    public function create(User $user)
    {
        return $user->role_id === 3; // Supposons que le rôle admin ait l'ID 1
    }
}
