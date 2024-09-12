<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DetteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Http\Requests\CreateDetteRequest;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    public function getDette($id)
    {
        $dette = $this->detteService->getDetteById($id);
        return [
           'dette'=>$dette, 
           'statut'=>200,
           'message'=>'Dette trouvée'
        
        ];
    }

    public function createDette(CreateDetteRequest $request)
    {
        $dette = $this->detteService->createDette($request->validated());
        return [
            'dette'=>$dette, 
            'statut'=>200,
            'message'=>'Dette trouvée'
         
         ];
    }

   
    public function addArticlesToDette(Request $request, $detteId)
{
    $articlesData = $request->input('articles');
    // Ensure the articles data includes 'article_id'
    $this->detteService->addArticlesToDette($articlesData, $detteId);
    return [
        'articlesData'=>$articlesData, 
        'statut'=>200,
        'message'=>'Dette trouvée'
    ];
    
}

    public function getAllDettes(Request $request)
    {
        // Récupérer le filtre statut via les paramètres de requête (e.g., ?statut=solde)
        $statut = $request->query('statut');
    
        // Déterminer le statut selon la valeur du paramètre
        $isSolde = null;
        if ($statut === 'solde') {
            $isSolde = true;
        } elseif ($statut === 'nonSolde') {
            $isSolde = false;
        }
    
        // Appel au service pour récupérer les dettes filtrées
        $result = $this->detteService->getAllDettes($isSolde);
    
        // Retourner la réponse avec les dettes et un message
        return [
            'result'=>$result, 
            'statut'=>200,
            'message'=>'Dette trouvée'
        ];
    }
    
    
    
}
