<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use App\Models\User;
class AuthentificationSanctum implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials)
{
    if (!Auth::attempt($credentials)) {  
        throw new \Exception('Information fournie incorrecte pour le login: ' . $credentials['login']);
    }

    $user = User::where('login', $credentials['login'])->firstOrFail();
    
    // Créer le token avec Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    return [
        'success' => true,
        'user' => $user,
        'token' => $token,
    ];
}
    
    

    public function logout()
    {
        // Implement logout logic for Sanctum
    }
}
 