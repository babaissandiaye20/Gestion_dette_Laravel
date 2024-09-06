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
        // Requête de base sans chargement de la relation 'user'
        $query = Client::query();  // Par défaut, ne charge pas 'user'
    
        // Si un filtre est appliqué, charger les utilisateurs associés
        if ($comptes || $actif) {
            $query->with('user');  // Charger les utilisateurs uniquement si un filtre est présent
        }
    
        // Filtrer sur l'existence d'un compte utilisateur associé si 'comptes' est défini
        if ($comptes === 'oui') {
            // Clients avec un utilisateur associé (user_id non null)
            $query->whereNotNull('user_id');
        } elseif ($comptes === 'non') {
            // Clients sans utilisateur associé (user_id null)
            $query->whereNull('user_id');
        }
    
        // Filtrer sur l'état de l'utilisateur associé (si applicable)
        if ($actif === 'oui') {
            // Clients avec un utilisateur et dont l'état est 'actif'
            $query->whereHas('user', function($q) {
                $q->where('etat', 'actif');
            });
        } elseif ($actif === 'non') {
            // Clients avec un utilisateur et dont l'état est 'inactif'
            $query->whereHas('user', function($q) {
                $q->where('etat', 'inactif');
            });
        }
    
        // Retourne les résultats paginés
        return $query->paginate(10);
    }
    
    
    
    public function afficherDettes($clientId)
    {
        return Dette::where('client_id', $clientId)->with('articles', 'details')->get();
    }
    
}
