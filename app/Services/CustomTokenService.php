<?php
namespace App\Services;

class CustomTokenService
{
    public function createPersonalAccessToken($user, $name, array $scopes = [])
    {
        // Créer un token en utilisant la méthode sur le modèle utilisateur
        $token = $user->createToken($name, $scopes)->accessToken;

        // Ajouter des données personnalisées au token
        // Vous devrez probablement gérer cela dans la base de données
        // ou en utilisant un modèle personnalisé pour le token

        return $token;
    }
}
