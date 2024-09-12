<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientCreateRequest;
use App\Facades\ClientServiceFacade;
use App\Models\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function getClientsWithFilters(Request $request)
    {
        $this->authorize('create', Client::class); 

        $comptes = $request->query('comptes');
        $etat = $request->query('actif');

        $clients = ClientServiceFacade::getClientsWithFilters($comptes, $etat);

        return [
            'status' => $clients->isEmpty() ? 403 : 200,
            'message' => $clients->isEmpty() ? 'Aucun client trouvé.' : 'Clients trouvés.',
            'clients' => $clients
        ];
    }

    public function register(ClientRequest $request)
    {
        $this->authorize('create', Client::class);

        try {
            $client = $request->has('client_id') 
                ? ClientServiceFacade::registerUserForClient($request, $request->input('client_id'))
                : ClientServiceFacade::createClient($request);

            return [
                'statut' => 201,
                'message' => $request->has('client_id') 
                    ? 'Utilisateur enregistré pour le client avec succès.' 
                    : 'Client créé avec succès.',
                'client' => $client
            ];
        } catch (\Exception $e) {
            return ['statut' => 500, 'message' => $e->getMessage()];
        }
    }

    public function create(ClientCreateRequest $request)
    {
        $this->authorize('create', Client::class);

        $client = ClientServiceFacade::create($request->validated());
        return ['statut' => 201, 'message' => 'Client créé avec succès.', 'client' => $client];
    }

    public function getClientsByTelephones(Request $request)
    {
        $this->authorize('create', Client::class);

        $clients = ClientServiceFacade::getClientsByTelephones(explode(',', $request->input('telephones')));
        return ['statut' => 200, 'clients' => $clients];
    }

    public function getClientById($id)
    {
        $this->authorize('create', Client::class);

        $client = ClientServiceFacade::getClientById($id);
        return ['statut' => 200, 'client' => $client];
    }

    public function getClientWithUser($id)
    {
        $this->authorize('create', Client::class);
        $client = ClientServiceFacade::getClientWithUser($id);
        return ['statut' => 200, 'client' => $client];
    }

    public function afficherDettes($clientId)
    {
        $this->authorize('create', Client::class);

        $dettes = ClientServiceFacade::afficherDettes($clientId);
        return ['statut' => 200, 'dettes' => $dettes];
    }
    
}
