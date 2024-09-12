<?php 
namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Models\Article;

class ArticleServiceImpl implements ArticleService
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function all()
    {
        return $this->articleRepository->all();
    }

    public function create(array $data)
    {
        return $this->articleRepository->create($data);
    }

    public function find($id)
    {
        return $this->articleRepository->find($id);
    }

    public function update($id, array $data)
{
    // Récupérer l'article correspondant à l'ID
    $article = $this->articleRepository->find($id);
    
    if (!$article) {
        // Si l'article n'est pas trouvé, vous pouvez gérer l'erreur ici
        return [
            'error' => 'Article non trouvé',
            'articleId' => $id,
        ];
    }
    
    // Vérifier si la quantité est définie dans les données d'entrée
    if (isset($data['qutestock'])) {
        $newQuantity = $data['qutestock'];
        
        // Vérifier si la quantité est valide (non négative)
        if ($newQuantity < 0) {
            return [
                'error' => 'Quantité invalide',
                'article' => $article,
                'quantity' => $newQuantity,
            ];
        }

        // Ajouter la nouvelle quantité à la quantité existante
        $data['qutestock'] = $article->qutestock + $newQuantity;
    }

    // Effectuer la mise à jour dans la base de données
    return $this->articleRepository->update($id, $data);
}

    public function delete($id)
    {
        return $this->articleRepository->delete($id);
    }

    public function findByLibelle($libelle)
    {
        return $this->articleRepository->findByLibelle($libelle);
    }
    public function updateQuantities(array $articles)
    {
        $articlesWithErrors = [];

        foreach ($articles as $articleData) {
            $articleId = $articleData['articleId'] ?? null;
            $quantity = $articleData['quantity'] ?? null;

            if (is_null($articleId) || is_null($quantity)) {
                $articlesWithErrors[] = [
                    'articleId' => $articleId,
                    'quantity' => $quantity,
                    'error' => 'Données manquantes',
                ];
                continue;
            }

            $article = $this->articleRepository->find($articleId);

            if ($article) {
                if ($quantity < 0) {
                    $articlesWithErrors[] = [
                        'article' => $article,
                        'quantity' => $quantity,
                        'error' => 'Quantité invalide',
                    ];
                } else {
                    // Mettre à jour la quantité en stock
                    $this->articleRepository->update($articleId, [
                        'qutestock' => $article->qutestock + $quantity
                    ]);
                }
            } else {
                $articlesWithErrors[] = [
                    'articleId' => $articleId,
                    'quantity' => $quantity,
                    'error' => 'Article non trouvé',
                ];
            }
        }

        return $articlesWithErrors;
    }

    public function findByEtat($disponible)
    {
        return $this->articleRepository->findByEtat($disponible);
    }
}
