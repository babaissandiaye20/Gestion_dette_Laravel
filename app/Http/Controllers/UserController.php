<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Traits\StatuesTrait;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Laravel\Passport\HasApiTokens;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use App\Services\UserServiceInterface;
use App\Models\Role; // Import du modèle Role
use App\Services\AuthentificationServiceInterface;
use Illuminate\Auth\Access\AuthorizationException; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Ajoutez ceci

class UserController extends \Illuminate\Routing\Controller
{
    protected $authService;
    protected $userService;
    public function __construct(AuthentificationServiceInterface $authService,UserServiceInterface $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    use StatuesTrait, HasApiTokens,AuthorizesRequests;

    // Création d'un utilisateur

    public function create(UserRequest $request)
    {
        $this->authorize('create', User::class); 

        try {
            if ($request->password !== $request->password_confirmation) {
                return response()->json($this->response(
                    \App\Enums\Statues::ECHEC(),
                    null,
                    'La confirmation du mot de passe ne correspond pas.'
                ), 400);
            }

            $user = $this->userService->createUser($request->all());

            return response()->json($this->response(
                \App\Enums\Statues::SUCCESS(),
                ['user' => $user],
                'Utilisateur créé avec succès.'
            ), 201);

        } catch (\Exception $e) {
            return response()->json($this->response(
                \App\Enums\Statues::ECHEC(),
                null,
                $e->getMessage()
            ), 500);
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
       /*   dd($credentials);  */
        $authResponse = $this->authService->authenticate($credentials);
    
        // Debugging output (you can remove this later)
     /*  dd($authResponse);  */ 
    
        // Check if $authResponse is an array and 'success' key exists
        if (is_array($authResponse) && isset($authResponse['success']) && $authResponse['success']) {
            // Ensure 'user' and 'token' exist in the response
            if (isset($authResponse['user']) && isset($authResponse['token'])) {
                // Use the user returned by the authService instead of retrieving it again
                $user = $authResponse['user'];
                
                // Generate a secure refresh token
                $refreshToken = Str::random(100);
    
                // Save or update the refresh token for the user in the database
                $user->update(['refresh_token' => $refreshToken]);
    
              return  [
                    'access_token' => $authResponse['token'],
                    'token_type' => 'Bearer',
                    'refresh_token' => $refreshToken,
                    'user' => $user,
                ];
            }
        }
    
        // Return unauthorized response if authentication failed
        return ['error' => 'Unauthorized'];
    }
    
    public function index(Request $request)
    {
        $this->authorize('create', User::class);
        
        // Récupérer les paramètres de requête
        $roleFilter = $request->query('roles');
        $etatFilter = $request->query('actif');
    
        // Initialiser la requête de base pour les utilisateurs
        $query = User::query();
    
        // Filtrer par rôle si le filtre est fourni
        if ($roleFilter) {
            // Trouver l'ID du rôle correspondant au nom fourni
            $role = Role::where('name', $roleFilter)->first();
            if ($role) {
                $query->where('role_id', $role->id);
            } else {
                // Si aucun rôle n'est trouvé, retourner une réponse vide
                return response()->json([
                    'status' => 403,
                    'message' => 'Aucun utilisateur trouvé pour le rôle spécifié.',
                    'users' => []
                ], 403);
            }
        }
    
        // Filtrer par état actif si le filtre est fourni
        if ($etatFilter === 'oui') {
            $query->where('etat', 'actif');
        } elseif ($etatFilter === 'non') {
            $query->where('etat', 'inactif');
        }
    
        // Paginer les résultats
        $users = $query->paginate(10);
    
        // Vérifier s'il y a des utilisateurs trouvés
        if ($users->isEmpty()) {
            return response()->json([
                'status' => 403,
                'message' => 'Aucun utilisateur trouvé.',
                'users' => []
            ], 403);
        }
    
        // Retourner les résultats paginés en format JSON avec un message de succès
        return response()->json([
            'status' => 200,
            'message' => 'Utilisateurs trouvés.',
            'users' => $users
        ], 200);
    }
    
}
