<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\Dette;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository implements ClientRepositoryInterface
{
    public function create(array $data)
    {
        return Client::create($data);
    }
    public function getClientById($id)
    {
        return Client::find($id);
    }

    public function findClientById($id)
    {
        return Client::with('user')->find($id);
    }

    
    public function findClientWithUserById($id)
    {
        return Client::with('user')->find($id);
    }
    
        public function getClientWithUser($clientId)
        {
            return Client::with('user')->find($clientId);
        }
    public function getClientsByTelephones(array $telephones)
    {
        return Client::whereIn('telephone', $telephones)->with('user')->get();
    }

    public function getClientsWithFilters(?string $comptes, ?string $actif): LengthAwarePaginator
{
    $query = Client::query();

    // Filtrer sur l'existence d'un compte utilisateur associé
    if ($comptes === 'oui') {
        $query->withUsers();
    } elseif ($comptes === 'non') {
        $query->withoutUsers();
    }

    // Filtrer sur l'état de l'utilisateur associé
    if ($actif === 'oui') {
        $query->active();
    } elseif ($actif === 'non') {
        $query->inactive();
    }

    return $query->paginate(10);
}

    
    
    
    public function afficherDettes($clientId)
    {
        return Dette::where('client_id', $clientId)->with('articles', 'details')->get();
    }
    
}
