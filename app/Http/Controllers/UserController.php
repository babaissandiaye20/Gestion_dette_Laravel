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
use App\Services\AuthentificationServiceInterface;
class UserController extends \Illuminate\Routing\Controller
{
    protected $authService;
    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    use StatuesTrait, HasApiTokens,AuthorizesRequests;

    // Création d'un utilisateur
public function create(UserRequest $request)
{
  /*   $this->authorize('create', User::class); */
    try {
        // Authorize the action
    
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
