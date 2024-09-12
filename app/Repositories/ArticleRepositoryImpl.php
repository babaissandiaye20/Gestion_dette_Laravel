<?php
namespace App\Repositories;

use App\Models\Article;

class ArticleRepositoryImpl implements ArticleRepository
{
    public function all()
    {
        return Article::all();
    }

    public function create(array $data)
    {
        return Article::create($data);
    }

    public function find($id)
    {
        return Article::find($id);
    }

    public function update($id, array $data)
    {
        $article = Article::find($id);
        if ($article) {
            $article->update($data);
            return $article;
        }
        return null;
    }

    public function delete($id)
    {
        $article = Article::find($id);
        if ($article) {
            $article->delete();
            return true;
        }
        return false;
    }

    // Utilisation du scope 'libelle' pour filtrer par libelle
    public function findByLibelle($libelle)
    {
        return Article::libelle($libelle)->first();
    }

    // Utilisation du scope 'disponible' pour filtrer par disponibilité
    public function findByEtat($disponible)
    {
        // Utiliser le scope disponible et retourner un Query Builder
        return Article::disponible($disponible); // Pas de `get()`, juste la requête
    }
    
}
