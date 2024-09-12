<?php

namespace App\Services;

use App\Repository\DetteRepository;
use App\Models\Article;
use App\Models\Paiement;
use App\Repositories\DetteRepositories;
use Illuminate\Support\Facades\DB;
use Exception;

class DetteServiceImpl implements DetteService
{
    protected $detteRepository;

    public function __construct(DetteRepositories $detteRepository)
    {
        $this->detteRepository = $detteRepository;
    }

    public function getArticlesByDetteId(int $id)
    {
        return $this->detteRepository->findArticlesByDetteId($id);
    }

    public function getPaiementsByDetteId(int $id)
    {
        return $this->detteRepository->findPaiementsByDetteId($id);
    }

    public function getClientDettes(int $clientId)
    {
        return $this->detteRepository->getDettesByClientId($clientId);
    }

    public function getDetteById($id)
    {
        return $this->detteRepository->findById($id);
    }

    public function createDette(array $data)
    {
        DB::beginTransaction();
        try {
            $dette = $this->detteRepository->create([
                'client_id' => $data['client_id'],
                'montant' => $data['montant'],
            ]);
    
            foreach ($data['articles'] as $articleData) {
                $article = Article::find($articleData['articleId']);
                if ($article && $article->qutestock >= $articleData['qustock']) {  // Using qutestock
                    $dette->articles()->attach($articleData['articleId'], [
                        'qte_vente' => $articleData['qustock'],
                        'prix_vente' => $articleData['prix'],
                    ]);
    
                    $article->qutestock -= $articleData['qustock'];  // Using qutestock
                    $article->save();
                } else {
                    throw new Exception('Stock insuffisant pour l\'article: ' . $article->libelle);
                }
            }
    
            if (isset($data['paiement']['montant'])) {
                if ($data['paiement']['montant'] > $dette->montant) {
                    throw new Exception('Le montant du paiement ne peut pas dépasser la dette.');
                }
    
                Paiement::create([
                    'dette_id' => $dette->id,
                    'montant' => $data['paiement']['montant'],
                ]);
            }
    
            DB::commit();
            return $dette->load('client', 'articles');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function addArticlesToDette(array $articlesData, int $detteId)
    {
        DB::beginTransaction();
        try {
            // Retrieve the debt by its ID
            $dette = $this->detteRepository->findById($detteId);
    
            foreach ($articlesData as $articleData) {
                // Check if 'article_id' or another key is used
                $articleId = $articleData['article_id'] ?? $articleData['articleId'] ?? null;

                if (!$articleId) {
                    throw new Exception('article_id is missing');
                }
    
                // Find the article by ID
                $article = Article::find($articleId);
                
                // Check if the article exists and if there is enough stock
                if ($article && $article->qutestock >= $articleData['qustock']) {
                    
                    // Attach the article to the debt with the specified quantity and price
                    $dette->articles()->attach($articleId, [
                        'qte_vente' => $articleData['qustock'],
                        'prix_vente' => $articleData['prix'],
                    ]);
    
                    // Deduct the sold quantity from the stock
                    $article->qutestock -= $articleData['qustock'];
                    $article->save();
                } else {
                    throw new Exception('Stock insuffisant pour l\'article: ' . $article->libelle);
                }
            }
    
            // Commit the transaction
            DB::commit();
            return $dette->load('articles');
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            throw $e;
        }
    }
    



public function getAllDettes($isSolde = null)
{
    // Appel à la méthode du repository pour obtenir les dettes filtrées
    $dettes = $this->detteRepository->getAllDettes($isSolde);

    // Si aucune dette n'est trouvée, retourner un message avec un tableau vide
    if ($dettes->isEmpty()) {
        return [
            'status' => 404,
            'message' => $isSolde ? 'Aucune dette soldée trouvée.' : 'Aucune dette non soldée trouvée.',
            'data' => []
        ];
    }

    // Sinon, retourner les dettes
    return [
        'status' => 201,
        'message' => 'Dettes récupérées avec succès.',
        'data' => $dettes
    ];
}


}
