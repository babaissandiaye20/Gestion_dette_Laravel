<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ArticleController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    public function store(StoreArticleRequest $request)
    {
        $this->authorize('create', Article::class); 
        $article = Article::create($request->validated());

        return response()->json([
            'message' => 'Article créé avec succès!',
            'article' => $article,
        ], 201);
    }

    
    public function show($id)
    {
        $this->authorize('create', Article::class); 
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => 404,
                'message' => "L'article avec l'ID $id n'existe pas.",
                'article' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Article trouvé.',
            'article' => $article
        ], 200);
    }
    


    public function update(UpdateArticleRequest $request, $id)
    {
        $this->authorize('create', Article::class); 
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => "L'article avec l'ID $id n'existe pas.",
            ], 404);
        }

        $article->update($request->validated());

        return response()->json([
            'message' => 'Article mis à jour avec succès!',
            'article' => $article,
        ], 200);
    }

  
    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => "L'article avec l'ID $id n'existe pas.",
            ], 404);
        }

        $article->delete();

        return response()->json([
            'message' => 'Article supprimé avec succès!',
        ], 200);
    }

    
    public function updateQuantities(Request $request)
    {
        $this->authorize('create', Article::class); 
        $articles = $request->input('articles', []);
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

            $article = Article::find($articleId);

            if ($article) {
                if ($quantity < 0) {
                    $articlesWithErrors[] = [
                        'article' => $article,
                        'quantity' => $quantity,
                        'error' => 'Quantité invalide',
                    ];
                } else {
                    $article->qutestock += $quantity;
                    $article->save();
                }
            } else {
                $articlesWithErrors[] = [
                    'articleId' => $articleId,
                    'quantity' => $quantity,
                    'error' => 'Article non trouvé',
                ];
            }
        }

        if (count($articlesWithErrors) > 0) {
            return response()->json([
                'message' => 'Certaines mises à jour ont échoué.',
                'errors' => $articlesWithErrors,
            ], 400);
        }

        return response()->json([
            'message' => 'Tous les articles ont été mis à jour avec succès.',
        ], 200);
    }

    
    public function destroybis($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => "L'article avec l'ID $id n'existe pas.",
            ], 404);
        }

        $article->delete();

        return response()->json([
            'message' => 'Article supprimé avec succès!',
        ], 200);
    }

    
    public function index(Request $request)
    {
        $this->authorize('create', Article::class); 
        $disponible = $request->query('disponible');

        $query = Article::query();

        if ($disponible === 'oui') {
            $query->where('qutestock', '>', 0);
        } elseif ($disponible === 'non') {
            $query->where('qutestock', '=', 0);
        }

        $articles = $query->paginate(10);

        if ($articles->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun article trouvé.',
                'articles' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Articles trouvés.',
            'articles' => $articles
        ], 200);
    }
    public function searchByLibelle(Request $request)
    {
        $this->authorize('create', Article::class); 
        $libelle = $request->input('libelle');

        if (!$libelle) {
            return response()->json([
                'status' => 400,
                'message' => "Le champ 'libelle' est requis.",
                'article' => []
            ], 400);
        }

        $article = Article::where('libelle', $libelle)->first();

        if (!$article) {
            return response()->json([
                'status' => 404,
                'message' => "Aucun article trouvé avec le libelle '$libelle'.",
                'article' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Article trouvé.',
            'article' => $article
        ], 200);
    }
    
}
