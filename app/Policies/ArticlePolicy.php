<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function create(User $user)
    {
        return $user->role_id === 2; // Replace with the actual role ID that should have access
    }
}