<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Models\users;
use App\Models\Client;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\StatuesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Importez la façade DB
use App\Models\Role;
class ClientController extends \Illuminate\Routing\Controller
{
    use StatuesTrait;

    public function create(ClientRequest $request)
    {
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
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la création du client.',
                'error' => $e->getMessage(),
            ], 500);
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

        // Créez un utilisateur avec le rôle fourni
        $user = User::create([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'login' => $userData['login'],
            'password' => Hash::make($userData['password']),
            'role_id' => $role->id,
        ]);

        // Associez l'utilisateur au client
        $client->user()->associate($user);
        $client->save();
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

    public function indexbis():JsonResponse{
        $data=Client::whereNotNull('user_id')->with('user')->paginate(1);
        return response()->json ($data);
    }
         
}
