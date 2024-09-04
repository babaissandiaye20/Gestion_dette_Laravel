<?php
namespace App\Http\Controllers;

use App\Services\ClientServiceInterface;
use Illuminate\Http\Request;
use App\Models\users;
use App\Models\Client;
use App\Models\Dette;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\StatuesTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use App\Facades\ClientRepositoryFacade;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;


class ClientController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function getClientsWithFilters()
    {
        $comptes = request()->query('comptes');
        $etat = request()->query('actif');
        
        $clients = ClientRepositoryFacade::getClientsWithFilters($comptes, $etat);

        if ($clients->isEmpty()) {
            return [
                'status' => 403,
                'message' => 'Aucun client trouvé.',
                'clients' => []
            ];
        }

        return [
            'status' => 200,
            'message' => 'Clients trouvés.',
            'clients' => $clients
        ];
    }

    public function register(ClientRequest $request)
    {
        $this->authorize('create', Client::class);

        DB::beginTransaction();
        try {
            if (!$request->has('client_id')) {
                $client = ClientRepositoryFacade::createClient($request);
                
                // Sauvegarder le fichier QR code
                

        
        // Générer le QR code et le sauvegarder dans le dossier public
      
                DB::commit();
                return ['statut' => 201, 'client' => $client];
            }

            $clientId = $request->input('client_id');
            $client = ClientRepositoryFacade::registerUserForClient($request, $clientId);
            
            DB::commit();
            return ['statut' => 201, 'message' => 'Utilisateur enregistré pour le client avec succès.', 'client' => $client];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['statut' => 500, 'message' => $e->getMessage()];
        }
    }

    public function create(ClientCreateRequest $request)
    {
        $this->authorize('create', Client::class);
        $clientData = $request->only(['surnom', 'telephone', 'adresse']);
        $client = ClientRepositoryFacade::create($clientData);
        return ['statut' => 201, 'message' => 'Client créé sans utilisateur avec succès.', 'client' => $client];
    }

    public function getClientsByTelephones(Request $request)
    {
        $this->authorize('create', Client::class);
        $telephones = explode(',', $request->input('telephones'));
        $clients = ClientRepositoryFacade::getClientsByTelephones($telephones);
        return ['statut' => 200, 'clients' => $clients];
    }

    public function getClientById($id)
    {
        $this->authorize('create', Client::class);
        $client = ClientRepositoryFacade::getClientById($id);
        return ['statut' => 200, 'client' => $client];
    }

    public function getClientWithUser(Request $request, $id)
    {
        $this->authorize('create', Client::class);
        $client = ClientRepositoryFacade::getClientWithUser($id);
        return ['statut' => 200, 'client' => $client];
    }

    public function afficherDettes($clientId)
    {
        $this->authorize('create', Client::class);
        $dettes = ClientRepositoryFacade::afficherDettes($clientId);
        return ['statut' => 200, 'dettes' => $dettes];
    }
}
