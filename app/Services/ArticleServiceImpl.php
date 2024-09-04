<?php 
namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Models\Article; // Import the Article model

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

    public function findByEtat($disponible)
    {
        // Ensure the Article model is imported
        $query = Article::query();
    
        if ($disponible !== null) {
            $query->disponible($disponible); // This calls the scopeDisponible method on the model
        }
    
        return $query; // Returning the Query Builder, not a Collection
    }
}
