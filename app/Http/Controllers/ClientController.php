<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Models\users;
use App\Models\Client;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\StatuesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Importez la façade DB
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Ajoutez ceci
use Illuminate\Auth\Access\AuthorizationException;

class ClientController extends \Illuminate\Routing\Controller
{
    use StatuesTrait,AuthorizesRequests;

    public function register(ClientRequest $request)
    {
        $this->authorize('create', Client::class);
        DB::beginTransaction();
    
        try {
            // Vérifiez si un client_id est fourni
            $clientId = $request->input('client_id');
    
            if ($clientId) {
                // Si un client_id est fourni, créez un utilisateur pour ce client
                $client = $this->registerUserForClient($request, $clientId);
            } else {
                // Sinon, créez un nouveau client et éventuellement un utilisateur
                $client = $this->createClient($request);
            }
    
            DB::commit();
    
            return response()->json($this->response(
                \App\Enums\Statues::SUCCESS(),
                ['client' => $client],
                'Client créé avec succès.'
            ), 201);
        } catch (AuthorizationException $e) {
            DB::rollBack();
    
            return response()->json([
                'statut' => 403,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action.",
                'data' => null
            ], 403);
        }
    }
    
    
    private function registerUserForClient($request, $clientId)
    {
        // Récupérez le client existant
        $client = Client::find($clientId);
    
        if (!$client) {
            throw new \Exception('Client not found');
        }
    
        // Vérifiez si le client a déjà un utilisateur associé
        if ($client->user_id) {
            throw new \Exception('Ce client a déjà un compte utilisateur.');
        }
    
        // Créez un utilisateur pour ce client
        $this->createUserForClient($request, $client);
    
        return $client;
    }
    
    private function createClient($request)
    {
       
        // Créez un nouveau client
        $client = new Client([
            'surnom' => $request->surnom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);
        $client->save();
    
        // Si des informations utilisateur sont fournies, créez un utilisateur
        if ($request->has(['nom', 'prenom', 'login', 'password', 'password_confirmation'])) {
            $this->createUserForClient($request, $client);
        }
    
        return $client;
    }
    

    
    private function createUserForClient($request, $client)
{
    // Récupérez l'ID du rôle depuis la requête
    $roleId = $request->input('role');

    if ($roleId) {
        $role = Role::find($roleId);

        if (!$role) {
            throw new \Exception('Role not found');
        }

        // Validez les données utilisateur avec UserRequest
        $userData = $request->only(['nom', 'prenom', 'login', 'password', 'password_confirmation']);

        // Log the user data for debugging

        $validator = Validator::make($userData, (new UserRequest())->rules(), (new UserRequest())->messages());

        if ($validator->fails()) {
            throw new \Exception(json_encode($validator->errors()));
        }
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }
        // Créez un utilisateur avec le rôle fourni
        $user = User::create([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'login' => $userData['login'],
            'password' => Hash::make($userData['password']),
            'role_id' => $role->id,
            'photo' => $photoPath,
        ]);

        // Associez l'utilisateur au client
        $client->user()->associate($user);
        $client->save();
    }
}
public function create(ClientCreateRequest $request)
{
    $this->authorize('create', Client::class);
    DB::beginTransaction();

    try {
        // Créez un nouveau client sans utilisateur
        $client = new Client([
            'surnom' => $request->surnom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);
        $client->save();

        DB::commit();

        return response()->json($this->response(
            \App\Enums\Statues::SUCCESS(),
            ['client' => $client],
            'Client créé sans utilisateur avec succès.'
        ), 201);
    } catch (AuthorizationException $e) {
        DB::rollBack();
        
        return response()->json([
            'statut' => 403,
            'message' => "Vous n'êtes pas autorisé à effectuer cette action.",
            'data' => null
        ], 403);
    }
}

    
    public function show($id)
    {
        // Rechercher le client avec l'utilisateur associé
        $client = DB::table('clients')
            ->leftJoin('users', 'clients.user_id', '=', 'users.id')
            ->select(
                'clients.id',
                'clients.surnom',
                'clients.telephone',
                'clients.adresse',
                'users.nom as user_nom',
                'users.prenom as user_prenom',
                'users.login as user_login',
                'users.role as user_role'
            )
            ->where('clients.id', $id)
            ->first();
    
        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client non trouvé.'
            ], 404);
        }
    
        // Organiser les données du client
        $formattedClient = [
            'client' => [
                'id' => $client->id,
                'surnom' => $client->surnom,
                'telephone' => $client->telephone,
                'adresse' => $client->adresse,
            ],
            'user' => $client->user_nom ? [
                'nom' => $client->user_nom,
                'prenom' => $client->user_prenom,
                'login' => $client->user_login,
                'role' => $client->user_role,
            ] : null
        ];
    
        return response()->json($this->response(
            \App\Enums\Statues::SUCCESS(),
            ['client' => $formattedClient],
            'Détails du client récupérés avec succès.'
        ), 200);
    }
    public function indexbis(): JsonResponse
    {
        $this->authorize('create', Client::class);
        // Récupérer les paramètres de requête
        $comptes = request()->query('comptes');
        $etat = request()->query('actif');
        
        // Initialiser la requête de base
        $query = Client::query();
    
        // Appliquer les filtres en fonction de la présence du user_id
        if ($comptes === 'oui') {
            $query->whereNotNull('user_id');
        } elseif ($comptes === 'non') {
            $query->whereNull('user_id');
        }
    
        // Appliquer les filtres en fonction de l'état de l'utilisateur associé
        if ($etat === 'oui') {
            $query->whereHas('user', function($q) {
                $q->where('etat', 'actif');
            });
        } elseif ($etat === 'non') {
            $query->whereHas('user', function($q) {
                $q->where('etat', 'inactif');
            });
        }
    
        // Charger les utilisateurs complets si des filtres sont appliqués
        $clients = $query->with('user')->paginate(10);
    
        // Vérifier s'il y a des clients trouvés
        if ($clients->isEmpty()) {
            return response()->json([
                'status' => 403,
                'message' => 'Aucun client trouvé.',
                'clients' => []
            ], 403);
        }
    
        // Retourner les résultats paginés en format JSON avec un message de succès
        return response()->json([
            'status' => 200,
            'message' => 'Clients trouvés.',
            'clients' => $clients
        ], 200);
    }
    
    public function getClientsByTelephones(Request $request)
    {
        // Authorize the action
        $this->authorize('create', Client::class);
    
        // Retrieve phone numbers from the request
        $telephones = $request->input('telephones');
    
        // Check if the input is valid
        if (empty($telephones)) {
            return response()->json([
                'status' => 400,
                'message' => 'Veuillez fournir au moins un numéro de téléphone.',
                'clients' => []
            ], 400);
        }
    
        // Convert the phone numbers into an array if they are comma-separated
        $telephoneArray = explode(',', $telephones);
    
        // Search for clients with the provided phone numbers
        $clients = Client::whereIn('telephone', $telephoneArray)->get();
    
        // Check if any clients were found
        if ($clients->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun client trouvé pour les numéros de téléphone fournis.',
                'clients' => []
            ], 404);
        }
    
        // Return the found clients
        return response()->json([
            'status' => 200,
            'message' => 'Clients trouvés.',
            'clients' => $clients
        ], 200);
    }
    
    public function getClientById($id): JsonResponse
    {
       /*  $this->authorize('create', Client::class); */
        // Trouver le client par son ID
        $client = Client::find($id);

        // Vérifier si le client existe
        if (!$client) {
            return response()->json([
                'status' => 404,
                'message' => 'Client non trouvé.',
                'client' => null
            ], 404);
        }

        // Retourner les informations du client trouvées
        return response()->json([
            'status' => 200,
            'message' => 'Client trouvé.',
            'client' => $client
        ], 200);
    }
    public function getClientWithUser(Request $request, $id): JsonResponse
    {
       /*  $this->authorize('create', Client::class); */
        // Trouver le client par son ID
        $client = Client::with('user')->find($id);

        // Vérifier si le client existe
        if (!$client) {
            return response()->json([
                'status' => 404,
                'message' => 'Client non trouvé.',
                'client' => null
            ], 404);
        }

        // Préparer la réponse avec ou sans utilisateur
        $response = [
            'status' => 200,
            'message' => 'Client trouvé.',
            'client' => $client
        ];

        // Vérifier si l'utilisateur associé au client existe
        if ($client->user_id) {
            // Inclure les informations de l'utilisateur
            $response['user'] = $client->user;
        } else {
            // Sinon, inclure user_id comme null
            $response['client']['user_id'] = null;
        }

        return response()->json($response, 200);
    }
    /* public function getDettesWithDetails($clientId)
    {
        // Vérifiez si le client existe
        $client = Client::find($clientId);
    
        if (!$client) {
            return response()->json([
                'status' => 404,
                'message' => 'Client non trouvé.',
            ], 404);
        }
    
        // Récupérez les dettes du client avec leurs détails
        $dettes = Dette::with('details')
                        ->where('client_id', $clientId)
                        ->get();
    
        // Vérifiez si des dettes sont trouvées
        if ($dettes->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Aucune dette trouvée pour ce client.',
                'dettes' => []
            ], 404);
        }
    
        // Retourner les dettes trouvées avec les détails
        return response()->json([
            'status' => 200,
            'message' => 'Dettes trouvées.',
            'dettes' => $dettes
        ], 200);
    } */
    
}