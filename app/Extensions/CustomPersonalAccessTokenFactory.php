<?php
namespace App\Extensions;

use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\Token;

class CustomPersonalAccessTokenFactory extends PersonalAccessTokenFactory
{
    /**
     * Create a personal access token.
     *
     * @param  \App\Models\User  $user
     * @param  string  $name
     * @param  array  $scopes
     * @return \Laravel\Passport\Token
     */
    public function create($user, $name, array $scopes = [])
    {
        $token = parent::create($user, $name, $scopes);

        // Add custom data to token
        $token->scopes = ['user_id' => $user->id];
        $token->save();

        return $token;
    }
}

