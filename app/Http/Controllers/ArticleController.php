<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateArticlesQuantitiesRequest;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends \Illuminate\Routing\Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        // Création de l'article
        $article = Article::create($request->validated());

        return response()->json([
            'message' => 'Article créé avec succès!',
            'article' => $article,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, $id)
    {
        // Recherche de l'article par ID
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => "L'article avec l'ID $id n'existe pas.",
            ], 404);
        }

        // Mise à jour de l'article
        $article->update($request->validated());

        return response()->json([
            'message' => 'Article mis à jour avec succès!',
            'article' => $article,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Recherche de l'article par ID
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => "L'article avec l'ID $id n'existe pas.",
            ], 404);
        }

        // Suppression de l'article
        $article->delete();

        return response()->json([
            'message' => 'Article supprimé avec succès!',
        ], 200);
    }

    /**
     * Update quantities for multiple articles.
     */
    public function updateQuantities(Request $request)
    {
        // Supposons que 'articles' est un tableau d'objets avec 'articleId' et 'quantity'
        $articles = $request->input('articles', []);
        $articlesWithErrors = [];

        foreach ($articles as $articleData) {
            $articleId = $articleData['articleId'] ?? null;
            $quantity = $articleData['quantity'] ?? null;

            // Vérifier que les données sont présentes
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
                // Vérifier si la quantité est valide
                if ($quantity < 0) {
                    $articlesWithErrors[] = [
                        'article' => $article, // Inclure les informations de l'article
                        'quantity' => $quantity,
                        'error' => 'Quantité invalide',
                    ];
                } else {
                    // Mettre à jour la quantité
                    $article->qutestock += $quantity;
                    $article->save();
                }
            } else {
                // Ajouter l'article aux erreurs s'il n'existe pas
                $articlesWithErrors[] = [
                    'articleId' => $articleId,
                    'quantity' => $quantity,
                    'error' => 'Article non trouvé',
                ];
            }
        }

        // Retourner les articles avec erreurs ou un tableau vide si tout est bon
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
    // Recherche de l'article par ID
    $article = Article::find($id);

    if (!$article) {
        return response()->json([
            'message' => "L'article avec l'ID $id n'existe pas.",
        ], 404);
    }

    // Soft delete de l'article
    $article->delete();

    return response()->json([
        'message' => 'Article supprimé avec succès!',
    ], 200);
}

}    