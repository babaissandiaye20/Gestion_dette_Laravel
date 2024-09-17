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
    // Fetch debts with their IDs for the given client without including articles
    return Dette::where('client_id', $clientId)->get(['id', 'montant', 'client_id']);
}


public function getClientWithDebtswithArticle()
{
    $clients = Client::with(['dettes' => function ($query) {
            $query->where('status', 'settled')->with('articles');
        }])
        ->get()
        ->filter(function ($client) {
            return $client->dettes->isNotEmpty();
        })
        ->map(function ($client) {
            return [
                'client' => [
                    'client_id' => $client->id,
                    'name' => $client->surnom,
                    'phone' => $client->telephone,
                    'debts' => $client->dettes->map(function ($dette) {
                        return [
                            'id' => $dette->id,  // Add the debt ID here
                            'amount' => $dette->montant,
                            'status' => 'settled',
                            'articles' => $dette->articles->mapWithKeys(function ($article) {
                                return [
                                    $article->id => [
                                        'name' => $article->libelle,
                                        'price' => $article->prix,
                                    ]
                                ];
                            }),
                        ];
                    }),
                ]
            ];
        });

    return $clients;
}

public function getClientWithDebtswithArticleForMongo()
{
    $clients = Client::with(['dettes' => function ($query) {
            $query->where('status', 'settled')->with('articles');
        }])
        ->get()
        ->filter(function ($client) {
            return $client->dettes->isNotEmpty();
        })
        ->map(function ($client) {
            return [
                'client' => [
                    'client_id' => $client->id,
                    'name' => $client->surnom,
                    'phone' => $client->telephone,
                    'debts' => $client->dettes->map(function ($dette) {
                        return [
                            'id' => $dette->id,  // Add the debt ID here
                            'amount' => $dette->montant,
                            'status' => 'settled',
                            'articles' => $dette->articles->map(function ($article) {
                                return [
                                    'article_id' => $article->id,
                                    'name' => $article->libelle,
                                    'price' => $article->prix,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ]
            ];
        });

    return $clients->toArray();
}

}
