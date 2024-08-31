<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role; // Import du modèle Role
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Str;
use App\Traits\StatuesTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Laravel\Passport\HasApiTokens;
use App\Http\Requests\LoginRequest;
class UserController extends \Illuminate\Routing\Controller
{
    use StatuesTrait, HasApiTokens;

    // Création d'un utilisateur
    public function create(UserRequest $request)
{
    // Vérification si la confirmation du mot de passe correspond
    if ($request->password !== $request->password_confirmation) {
        return response()->json($this->response(
            \App\Enums\Statues::ECHEC(),
            null,
            'La confirmation du mot de passe ne correspond pas.'
        ), 400);

    }

    // Récupérer l'id du rôle fourni
    $roleId = $request->role;
    /* dd($roleId); */

    // Création de l'utilisateur
    $user = User::create([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'login' => $request->login,
        'password' => Hash::make($request->password),
        'role_id' => $roleId, // Assigner l'id du rôle ici
    ]);

    // Retourne une réponse en utilisant le trait
    return response()->json($this->response(
        \App\Enums\Statues::SUCCESS(),
        ['user' => $user],
        'Utilisateur créé avec succès.'
    ), 201);
}
    // Suppression d'un utilisateur
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }

    // Login avec création de token
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('login', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = User::find(Auth::user()->id);
            $token = $user->createToken('LaravelPassportAuth')->accessToken;
            $refreshToken = Str::random(100); 
    
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'refresh_token' => $refreshToken,
                'user' => $user,
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
