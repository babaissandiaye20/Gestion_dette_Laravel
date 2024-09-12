<?php
namespace App\Observers;

use App\Models\Paiement;

class PaiementObserver
{
    // Cette méthode sera déclenchée après chaque paiement créé ou mis à jour
    public function saved(Paiement $paiement)
    {
        // Récupérer la dette associée au paiement
        $dette = $paiement->dette;

        // Appeler setStatus() pour mettre à jour le statut de la dette
        $dette->setStatus();
    }
}
