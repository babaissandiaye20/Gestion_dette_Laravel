<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientCreateRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Facades\ClientRepositoryFacade;
use App\Services\QRCodeService;
class ClientService implements ClientServiceInterface
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function register(ClientRequest $request)
    {
        DB::beginTransaction();
        try {
            // Cas 1 : Créer un client avec ou sans informations utilisateur
            if (!$request->has('client_id')) {
                $client = ClientRepositoryFacade::createClient($request);
                DB::commit();

                // Vérifie si les informations utilisateur sont fournies
               
                return response()->json(['statut' => 201,  'client' => $client], 201);
            }

            // Cas 2 : Enregistrer un utilisateur pour un client existant
            $clientId = $request->input('client_id');
            $client = ClientRepositoryFacade::registerUserForClient($request, $clientId);
            // Générer le contenu du QR code
            

// Appeler le service pour générer le QR code

            DB::commit();

            return response()->json(['statut' => 201, 'message' => 'Utilisateur enregistré pour le client avec succès.', 'client' => $client], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['statut' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function create(ClientCreateRequest $request)
    {
        $clientData = $request->only(['surnom', 'telephone', 'adresse']);
        $client = ClientRepositoryFacade::create($clientData);
        return response()->json(['statut' => 201, 'message' => 'Client créé sans utilisateur avec succès.', 'client' => $client], 201);
    }

    public function getClientsByTelephones(Request $request)
    {
        $telephones = explode(',', $request->input('telephones'));
        $clients = ClientRepositoryFacade::getClientsByTelephones($telephones);

        return response()->json(['statut' => 200, 'clients' => $clients], 200);
    }

    public function getClientById($id)
    {
        $client = ClientRepositoryFacade::getClientById($id);
        return response()->json(['statut' => 200, 'client' => $client], 200);
    }

    public function getClientWithUser(Request $request, $id)
    {
        $client = ClientRepositoryFacade::getClientWithUser($id);
        return response()->json(['statut' => 200, 'client' => $client], 200);
    }

    public function afficherDettes($clientId)
    {
        $dettes = ClientRepositoryFacade::afficherDettes($clientId);
        return response()->json(['statut' => 200, 'dettes' => $dettes], 200);
    }

    public function getClientsWithFilters(?string $comptes, ?string $etat): LengthAwarePaginator
    {
        return ClientRepositoryFacade::getClientsWithFilters($comptes, $etat);
    }
}
