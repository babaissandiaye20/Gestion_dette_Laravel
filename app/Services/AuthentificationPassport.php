<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials)
    {
        // Valider les informations d'identification entrantes
        $validator = Validator::make($credentials, [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Essayer d'authentifier l'utilisateur en utilisant le champ 'login'
        if (Auth::attempt(['login' => $credentials['login'], 'password' => $credentials['password']])) {
            $user = User::where('login', $credentials['login'])->first(); // Récupérer l'utilisateur par login
            $token = $user->createToken('LaravelPassportAuth')->accessToken;

            return [
                'success' => true,
                'user' => $user,
                'token' => $token
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
    }

    public function logout()
    {
        // Implémenter la logique de déconnexion pour Passport
    }
}
