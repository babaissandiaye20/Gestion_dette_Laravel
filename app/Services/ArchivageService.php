<?php

namespace App\Services;

use App\Models\Dette;
use App\Models\DetailDette;
use App\Services\MongoDBService;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class ArchivageService
{
    protected $service;

    public function __construct(FirebaseService $firebaseService, MongoDBService $mongoDBService)
    {
        $serviceType = env('ARCHIVAGE_SERVICE', 'firebase');

        if ($serviceType === 'mongodb') {
            $this->service = $mongoDBService;
        } else {
            $this->service = $firebaseService;
        }

    }

    public function store($data)
    {
        // Archiver les données
        $result = $this->service->store($data);
        return $result;
    }

    public function retrieve($id = null, $date = null)
    {
        return $this->service->retrieve($id, $date);
    }

  public function archiverDebts($id = null, $date = null)
  {
      $id = $id ?? 253;  // Default ID if none is provided

      // Retrieve data from MongoDB or Firebase
      $dettes = $this->retrieve($id, $date);

      // Debugging to check the retrieved data
      // dd($dettes);

      if (empty($dettes)) {
          Log::warning("Aucune dette trouvée pour l'ID : ${id}");
          return;
      }

      foreach ($dettes as $key => $dette) {
          // Firebase returns a structure where we have to dig into 'client' and 'debts' arrays
          if (isset($dette['client'])) {
              $client = $dette['client'];

              // Extract client_id
              $clientId = $client['client_id'] ?? 252;

              // Iterate through debts (since there might be multiple debts)
              if (isset($client['debts']) && is_array($client['debts'])) {
                  foreach ($client['debts'] as $debt) {
                      $montant = $debt['amount'] ?? 4000.0;
                      $status = $debt['status'] ?? 'settled';

                      // If clientId and amount exist, save the debt record
                      if ($clientId) {
                          try {
                              Dette::create([
                                  'client_id' => $clientId,
                                  'montant' => $montant,
                                  'status' => $status,
                              ]);
                              Log::info("Dette archivée avec succès pour le client ID : ${clientId}");
                          } catch (\Exception $e) {
                              Log::error("Erreur lors de l'archivage de la dette : " . $e->getMessage());
                          }
                      }
                  }
              } else {
                  Log::warning("Aucune dette trouvée pour le client ID : ${clientId}");
              }
          } else {
              Log::warning("Les données client ne sont pas disponibles pour l'ID : ${id}");
          }
      }
  }


}
