<?php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Policies\ArticlePolicy;
use App\Services\ArticleService;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ArticleController extends \Illuminate\Routing\Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }
    
    use AuthorizesRequests;

    public function store(StoreArticleRequest $request)
    {
        $this->authorize('create', Article::class); 
        $article = $this->articleService->create($request->validated());

        return [
            'status' => 201,
            'message' => 'Article créé avec succès!',
            'article' => $article,
        ];
    }

    public function show($id)
    {
        $this->authorize('create', Article::class); 
        $article = $this->articleService->find($id);

        if (!$article) {
            return [
                'status' => 404,
                'message' => "L'article avec l'ID $id n'existe pas.",
                'article' => [],
            ];
        }

        return [
            'status' => 200,
            'message' => 'Article trouvé.',
            'article' => $article
        ];
    }

    public function update(UpdateArticleRequest $request, $id)
    {
        $this->authorize('create', Article::class); 
        $article = $this->articleService->update($id, $request->validated());

        return [
            'message' => 'Article mis à jour avec succès!',
            'article' => $article,
        ];
    }

    public function destroy($id)
    {
        $this->authorize('delete', Article::class); 
        $this->articleService->delete($id);

        return ['message' => 'Article supprimé avec succès!'];
    }

    public function updateQuantities(Request $request)
    {
        $this->authorize('create', Article::class); 

        $articles = $request->input('articles', []);
        $articlesWithErrors = $this->articleService->updateQuantities($articles);

        if (!empty($articlesWithErrors)) {
            return [
                'statut'=>'400',
                'message' => 'Certaines mises à jour ont échoué.',
                'errors' => $articlesWithErrors,
            ];
        }

        return response()->json(['message' => 'Tous les articles ont été mis à jour avec succès.'], 200);
    }
    public function index(Request $request)
    {
        $disponible = $request->query('disponible');
    
        // La méthode findByEtat retourne un Query Builder, sur lequel vous pouvez paginer
        $articles = $this->articleService->findByEtat($disponible)->paginate(10);
    
        if ($articles->isEmpty()) {
            [
                'status' => 404,
                'message' => 'Aucun article trouvé.',
                'articles' => [],
            ];
        }
    
        return [
            'status' => 200,
            'message' => 'Articles trouvés.',
            'articles' => $articles
        ];
    }
    

    public function searchByLibelle(Request $request)
    {
        $libelle = $request->input('libelle');
    
        if (!$libelle) {
            return [
                'status' => 400,
                'message' => "Le champ 'libelle' est requis.",
            ];
        }
    
        $article = $this->articleService->findByLibelle($libelle);
    
        if (!$article) {
            return [
                'status' => 404,
                'message' => "Aucun article trouvé avec le libelle '$libelle'.",
                'article' => [],
            ];
        }
    
        return [
            'status' => 200,
            'message' => 'Article trouvé.',
            'article' => $article
        ];
    }
}
