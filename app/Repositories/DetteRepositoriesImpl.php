<?php

namespace App\Repositories;


use App\Models\Dette;

class DetteRepositoriesImpl implements DetteRepositories
{
    public function findArticlesByDetteId(int $id)
    {
        $dette = Dette::with('articles')->findOrFail($id);
        return $dette->articles;
    }

    public function findPaiementsByDetteId(int $id)
    {
        $dette = Dette::with('paiements')->findOrFail($id);
        return $dette->paiements;
    }

    public function getDettesByClientId(int $clientId)
    {
        return Dette::where('client_id', $clientId)->get();
    }

    public function create(array $data)
    {
        return Dette::create($data);
    }
    public function update(int $id, array $data)
    {
        $debt = Dette::find($id);
        if ($debt) {
            $debt->update($data);
            return $debt;
        }
        return null;
    }

    public function delete(int $id)
    {
        $debt = Dette::find($id);
        if ($debt) {
            return $debt->delete();
        }
        return false;
    }

    public function findById(int $id)
    {
        return Dette::with('client')->findOrFail($id);
    }

    public function findByClient(int $clientId)
    {
        return Dette::where('client_id', $clientId)->get();
    }
    public function getAllDettes($isSolde = null)
{
    if (is_null($isSolde)) {
        // Pagination de toutes les dettes
        return Dette::paginate(10);
    }

    // Utilisation du scope pour filtrer selon le statut et paginer les résultats
    return Dette::statut($isSolde ? 'Solde' : 'NonSolde')->paginate(10);
}

    
    
}
