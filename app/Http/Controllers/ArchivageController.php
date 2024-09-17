<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Services\ArchivageService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ArchivageController extends Controller
{
    protected $archivageService;

    public function __construct(ArchivageService $archivageService,)
    {
        $this->archivageService = $archivageService;
    }

    public function getArchivedClients(Request $request)
    {
        try {
            // Récupérer les clients archivés depuis Firebase ou MongoDB
            $id = $request->query('id');
            $date = $request->query('date');
            
            $archivedClients = $this->archivageService->retrieve($id, $date);

            if (empty($archivedClients)) {
                return response()->json(['message' => 'Aucune donnée trouvée.'], 404);
            }

            return response()->json($archivedClients, 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des clients archivés: ', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
    }
    public function insererDettesArchivées(Request $request)
    {
        // Récupérer l'ID ou la date à partir de la requête
        $id = $request->input('id');
        $date = $request->input('date');
        
        // Récupérer les données archivées via le service d'archivage
        $data = $this->archivageService->retrieve($id, $date);

        // Vérifier si des données ont été récupérées
        if (empty($data)) {
            return response()->json(['message' => 'Aucune donnée trouvée pour cette requête'], 404);
        }

        // Parcourir les données récupérées et les insérer dans la table dettes
        foreach ($data as $detteData) {
            // Extraire les informations nécessaires pour chaque dette
            $montant = $detteData['data']['montant'] ?? 0;
            $client_id = $detteData['data']['client_id'] ?? null;

            // Vérifier que les données essentielles existent
            if ($client_id && $montant) {
                // Insérer une nouvelle dette dans la table dettes
                Dette::create([
                    'client_id' => $client_id,
                    'montant' => $montant,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json(['message' => 'Les dettes ont été insérées avec succès'], 201);
    }
}
