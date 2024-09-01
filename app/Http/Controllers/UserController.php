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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Ajoutez ceci
use Illuminate\Auth\Access\AuthorizationException; 
class UserController extends \Illuminate\Routing\Controller
{
    use StatuesTrait, HasApiTokens,AuthorizesRequests;

    // Création d'un utilisateur
public function create(UserRequest $request)
{
    try {
        // Authorize the action
        $this->authorize('create', User::class);

        // Check if password confirmation matches
        if ($request->password !== $request->password_confirmation) {
            return response()->json($this->response(
                \App\Enums\Statues::ECHEC(),
                null,
                'La confirmation du mot de passe ne correspond pas.'
            ), 400);
        }
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoPath = $photo->storeAs('photos', uniqid() . '.' . $photo->getClientOriginalExtension(), 'public');
        }
        // Get the role ID provided in the request
        $roleId = $request->role;

        // Create the user
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'photo' => $photoPath
        ]);

        // Return a success response
        return response()->json($this->response(
            \App\Enums\Statues::SUCCESS(),
            ['user' => $user],
            'Utilisateur créé avec succès.'
        ), 201);

    } catch (AuthorizationException $e) {
        // Handle unauthorized action
        return response()->json($this->response(
            \App\Enums\Statues::ECHEC(),
            null,
            'Vous n\'êtes pas autorisé à créer un compte utilisateur.'
        ), 403);
    }
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
