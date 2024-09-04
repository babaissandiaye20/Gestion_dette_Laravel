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

    public function findByLibelle($libelle)
    {
        return Article::where('libelle', $libelle)->first();
    }

    public function findByEtat($etat)
    {
        return Article::where('etat', $etat)->get();
    }
}
