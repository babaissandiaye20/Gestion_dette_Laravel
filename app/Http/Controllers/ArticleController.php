<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateArticlesQuantitiesRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Ajoutez ceci
use Illuminate\Auth\Access\AuthorizationException;

class ArticleController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $this->authorize('create', Article::class); 
        // Création de l'article
        $article = Article::create($request->validated());

        return response()->json([
            'message' => 'Article créé avec succès!',
            'article' => $article,
        ], 201);
    }
    public function show($id)
{
    $this->authorize('create', Article::class); 
    // Recherche de l'article par ID
    $article = Article::find($id);

    // Vérifier si l'article est trouvé
    if (!$article) {
        return response()->json([
            'status' => 404,
            'message' => "L'article avec l'ID $id n'existe pas.",
            'article' => []
        ], 404);
    }

    // Retourner l'article trouvé avec un message de succès
    return response()->json([
        'status' => 200,
        'message' => 'Article trouvé.',
        'article' => $article
    ], 200);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, $id)
    {
        $this->authorize('create', Article::class); 
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
        $this->authorize('create', Article::class); 
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
public function index(Request $request)
{
    $this->authorize('create', Article::class); 
    // Récupérer le paramètre de requête 'disponible'
    $disponible = $request->query('disponible');

    // Initialiser la requête de base pour les articles
    $query = Article::query();

    // Appliquer le filtre basé sur la disponibilité des articles
    if ($disponible === 'oui') {
        $query->where('qutestock', '>', 0); // Correction ici
    } elseif ($disponible === 'non') {
        $query->where('qutestock', '=', 0); // Correction ici
    }

    // Récupérer les articles filtrés avec pagination
    $articles = $query->paginate(10);

    // Vérifier s'il y a des articles trouvés
    if ($articles->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => 'Aucun article trouvé.',
            'articles' => []
        ], 404);
    }

    // Retourner les résultats paginés en format JSON avec un message de succès
    return response()->json([
        'status' => 200,
        'message' => 'Articles trouvés.',
        'articles' => $articles
    ], 200);
}
public function searchByLibelle(Request $request)
{
    $this->authorize('create', Article::class); 
    // Récupérer le 'libelle' du corps de la requête
    $libelle = $request->input('libelle');

    // Valider que le libelle est fourni
    if (!$libelle) {
        return response()->json([
            'status' => 400,
            'message' => "Le champ 'libelle' est requis.",
            'article' => []
        ], 400);
    }

    // Recherche de l'article par libelle
    $article = Article::where('libelle', $libelle)->first();

    // Vérifier si l'article est trouvé
    if (!$article) {
        return response()->json([
            'status' => 404,
            'message' => "Aucun article trouvé avec le libelle '$libelle'.",
            'article' => []
        ], 404);
    }

    // Retourner l'article trouvé avec un message de succès
    return response()->json([
        'status' => 200,
        'message' => 'Article trouvé.',
        'article' => $article
    ], 200);
}


}    