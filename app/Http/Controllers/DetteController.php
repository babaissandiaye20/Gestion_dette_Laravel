<?php

namespace App\Http\Controllers;
use App\Enums\ClientCategory;
use App\Notifications\DemandeNotification;
use Illuminate\Http\Request;
use App\Services\DetteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Demande;
use App\Http\Requests\CreateDetteRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Dette;


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
           'dette' => $dette,
           'statut' => 200,
           'message' => 'Dette trouvée'
        ];
    }

    public function createDette(CreateDetteRequest $request)
    {
        $dette = $this->detteService->createDette($request->validated());
        return [
            'dette' => $dette,
            'statut' => 200,
            'message' => 'Dette trouvée'
        ];
    }

    public function addArticlesToDette(Request $request, $detteId)
    {
        $articlesData = $request->input('articles');
        // Ensure the articles data includes 'article_id'
        $this->detteService->addArticlesToDette($articlesData, $detteId);
        return [
            'articlesData' => $articlesData,
            'statut' => 200,
            'message' => 'Articles ajoutés à la dette'
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
            'result' => $result,
            'statut' => 200,
            'message' => 'Dettes récupérées'
        ];
    }

public function createDemande(Request $request)
{
    // Récupérer l'utilisateur authentifié
    $authenticatedUser = Auth::user();

    // Vérifier si l'utilisateur authentifié a le role_id = 3 et s'il a un client associé
    if ($authenticatedUser->role_id != 3 || !$authenticatedUser->client) {
        return response()->json(['error' => 'Non autorisé : Vous devez être un client avec un role_id de 3 pour créer une demande.'], 403);
    }

    $client = $authenticatedUser->client;
$user= $authenticatedUser->id;

    // Récupérer la catégorie du client
    $clientCategory = $client->category;

    // Récupérer les dettes et les paiements du client
    $dettes = $client->dettes;
    $totalDettes = $dettes->sum('montant');

    // Calculer le total des paiements effectués
    $totalPaiements = $dettes->sum(function ($dette) {
        return $dette->paiements->sum('montant');
    });

    if ($totalPaiements === null) {
        $totalPaiements = 0;
    }

   if (strtolower($clientCategory) === ClientCategory::BRONZE) {
       if ($totalPaiements < $totalDettes) {
           return response()->json(['error' => 'Vous ne pouvez pas faire une nouvelle demande tant que vos paiements totaux ne couvrent pas la totalité de votre dette.'], 403);
       }
   } elseif (strtolower($clientCategory) === ClientCategory::SILVER) {
       $maxDebtAmount = $client->max_debt_amount;
       if (($totalDettes - $totalPaiements) >= $maxDebtAmount) {
           return response()->json(['error' => 'Vous ne pouvez pas faire une nouvelle demande, car votre dette impayée dépasse la limite autorisée.'], 403);
       }
   }

    // Aucun contrôle spécifique pour les clients Gold

    // Valider le champ des articles pour s'assurer qu'il s'agit d'un tableau
    $request->validate([
        'articles' => 'required|array',
        'articles.*.article_id' => 'required|integer',
        'articles.*.quantity' => 'required|integer|min:1',
        'articles.*.price' => 'required|numeric|min:0',
    ], [
        'articles.required' => 'Le champ des articles est requis.',
        'articles.array' => 'Le champ des articles doit être un tableau.',
        'articles.*.article_id.required' => 'Chaque article doit avoir un article_id.',
        'articles.*.quantity.required' => 'Chaque article doit avoir une quantité.',
        'articles.*.price.required' => 'Chaque article doit avoir un prix.'
    ]);

    DB::beginTransaction();
    try {
        // Calculer le montant total de la demande
        $articles = $request->input('articles');
        $totalMontant = collect($articles)->sum(function ($article) {
            return $article['price'] * $article['quantity'];
        });

        // Insérer la demande avec l'ID du client associé
        $demande = DB::table('demandes')->insertGetId([
            'client_id' => $user,  // Utiliser l'ID du client associé
            'montant' => $totalMontant,  // Montant total calculé
            'articles' => json_encode($articles),  // Encoder les articles
        ]);

        // Notifier les utilisateurs avec le role_id = 2
        $this->notifyUserRoleDeux($demande, $client->id);

        DB::commit();
        return response()->json(['demande_id' => $demande, 'message' => 'Demande créée avec succès'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 400);
    }dd($user);
}





   public function notifyUserRoleDeux($demandeId, $clientId)
   {
       $users = User::where('role_id', 2)->get(); // Get all users with role 2

       foreach ($users as $user) {
           $user->notify(new DemandeNotification($demandeId, $clientId)); // Send notification via SMS and database
       }
   }

   public function confirmerDemande($demandeId)
   {
       $authenticatedUser = Auth::user();

       // Check if the user is authorized to confirm a demande
       if ($authenticatedUser->role_id != 2) {
           return response()->json(['error' => 'Unauthorized: Only users with role_id 2 can confirm a demande.'], 403);
       }

       DB::beginTransaction();
       try {
           // Retrieve the demande
           $demande = Demande::findOrFail($demandeId);

           // Check if the demande has already been processed
           if ($demande->statut !== 'en attente') {
               throw new \Exception('Cette demande a déjà été traitée.');
           }

           // Retrieve the client associated with the demande (via user_id)
           $user = User::findOrFail($demande->client_id);
           $client = $user->client;

           if (!$client) {
               throw new \Exception('Client non trouvé pour cet utilisateur.');
           }

           // Decode articles from the demande
           $articles = json_decode($demande->articles, true);

           // Check if articles are valid
           if (!$articles || !is_array($articles)) {
               throw new \Exception('Erreur lors du décodage des articles.');
           }

           // Prepare the articles array for dette creation
           $formattedArticles = [];
           foreach ($articles as $article) {
               if (!isset($article['article_id']) || !isset($article['quantity']) || !isset($article['price'])) {
                   throw new \Exception('Invalid article data: article_id, quantity, or price missing.');
               }

               $formattedArticles[] = [
                   'articleId' => $article['article_id'],
                   'qustock' => $article['quantity'],
                   'prix' => $article['price'],
               ];

               // Log each processed article
               \Log::info("Processing article ID: {$article['article_id']} with quantity: {$article['quantity']} and price: {$article['price']}");
           }

           // Prepare the debt data for the service
           $detteData = [
               'client_id' =>$user,
               'montant' => $demande->montant,
               'articles' => $formattedArticles, // Formatted articles array for the dette creation
           ];

           // Create the debt using the service
           $dette = $this->detteService->createDette($detteData);

           // Update the demande status to 'confirmée'
           $demande->update(['statut' => 'confirmée']);

           DB::commit();
           return response()->json(['success' => 'La demande a été confirmée et une dette a été créée.', 'dette' => $dette], 200);
       } catch (\Exception $e) {
           DB::rollBack();
           return response()->json(['error' => $e->getMessage()], 400);
       }
   }


       public function annulerDemande($demandeId)
       {
           $authenticatedUser = Auth::user();

           if ($authenticatedUser->role_id != 2) {
               return response()->json(['error' => 'Unauthorized: Only users with role_id 2 can cancel a demande.'], 403);
           }

           DB::beginTransaction();
           try {
               // Récupérer la demande avec le client associé
               $demande = Demande::with('client')->findOrFail($demandeId);

               if ($demande->statut !== 'en attente') {
                   throw new \Exception('Seules les demandes en attente peuvent être annulées.');
               }

               // Mettre à jour le statut de la demande
               $demande->update(['statut' => 'annulée']);

               // Notifier l'utilisateur concerné de l'annulation si le client est trouvé
               if ($demande->client) {
                   $demande->client->notify(new DemandeNotification($demande->id, $demande->client_id, 'annulée'));
               } else {
                   // Optionnel: Gérer le cas où le client n'existe pas
                   throw new \Exception('Le client associé à cette demande n\'existe pas.');
               }

               DB::commit();
               return response()->json(['success' => 'La demande a été annulée.'], 200);
           } catch (\Exception $e) {
               DB::rollBack();
               return response()->json(['error' => $e->getMessage()], 400);
           }
       }

       public function traiterDemande($demandeId, Request $request)
       {
           $authenticatedUser = Auth::user();

           // Vérifier si l'utilisateur est autorisé à confirmer ou annuler une demande
           if ($authenticatedUser->role_id != 2) {
               return response()->json(['error' => 'Unauthorized: Only users with role_id 2 can process a demande.'], 403);
           }

           $status = $request->input('status');  // Obtenir le statut depuis le corps de la requête
           $raison = $request->input('raison');  // Obtenir la raison pour l'annulation (si fournie)

           DB::beginTransaction();
           try {
               // Récupérer la demande avec le client associé
               $demande = Demande::with('client')->findOrFail($demandeId);

               // Vérifier si la demande est toujours en attente
               if ($demande->statut !== 'en attente') {
                   throw new \Exception('Cette demande a déjà été traitée.');
               }

               // Processus de confirmation
               if ($status === 'confirmation') {
                   // Décoder les articles de la demande
                   $articles = json_decode($demande->articles, true);

                   // Vérifier la validité des articles
                   if (!$articles || !is_array($articles)) {
                       throw new \Exception('Erreur lors du décodage des articles.');
                   }

                   // Récupérer le client associé à la demande
                   $user = User::findOrFail($demande->client_id);
                   $client = $user->client;

                   if (!$client) {
                       throw new \Exception('Client non trouvé pour cet utilisateur.');
                   }

                   // Préparer les articles pour la création de dette
                   $formattedArticles = [];
                   foreach ($articles as $article) {
                       if (!isset($article['article_id']) || !isset($article['quantity']) || !isset($article['price'])) {
                           throw new \Exception('Données d\'article non valides : article_id, quantity, ou price manquants.');
                       }

                       $formattedArticles[] = [
                           'articleId' => $article['article_id'],
                           'qustock' => $article['quantity'],
                           'prix' => $article['price'],
                       ];
                   }

                   // Préparer les données pour la dette
                   $detteData = [
                       'client_id' => $client->id,
                       'montant' => $demande->montant,
                       'articles' => $formattedArticles,
                   ];

                   // Créer la dette via le service
                   $dette = $this->detteService->createDette($detteData);

                   // Mettre à jour le statut de la demande en 'confirmée'
                   $demande->update(['statut' => 'confirmée']);

                   // Notifier le client de la confirmation (pas de raison ici, seulement la confirmation)
                   if ($demande->client) {
                       $demande->client->notify(new DemandeNotification($demande->id, $demande->client_id, 'confirmée'));
                   }

                   DB::commit();
                   return response()->json(['success' => 'La demande a été confirmée et une dette a été créée.', 'dette' => $dette], 200);

               } elseif ($status === 'annulation') {
                   // Processus d'annulation

                   // Mettre à jour le statut de la demande en 'annulée'
                   $demande->update(['statut' => 'annulée']);

                   // Notifier le client de l'annulation avec la raison
                   if ($demande->client) {
                       $demande->client->notify(new DemandeNotification($demande->id, $demande->client_id, 'annulée', $raison));
                   }

                   DB::commit();
                   return response()->json(['success' => 'La demande a été annulée avec la raison : ' . $raison], 200);
               } else {
                   throw new \Exception("Statut invalide : Le statut doit être 'confirmation' ou 'annulation'.");
               }
           } catch (\Exception $e) {
               DB::rollBack();
               return response()->json(['error' => $e->getMessage()], 400);
           }
       }



       public function relancerDemande($demandeId)
       {
           $authenticatedUser = Auth::user();

           DB::beginTransaction();
           try {
               // Récupérer la demande
               $demande = Demande::findOrFail($demandeId);

               if ($demande->statut !== 'annulée') {
                   throw new \Exception('Seules les demandes annulées peuvent être relancées.');
               }

               if ($authenticatedUser->id !== $demande->client_id) {
                   return response()->json(['error' => 'Unauthorized: Only the user who created the demande can relaunch it.'], 403);
               }

               // Mettre à jour le statut de la demande
               $demande->update(['statut' => 'en attente']);

               // Optionnel : Notifier l'utilisateur concerné de la relance
               $demande->client->notify(new DemandeNotification($demande->id, $demande->client_id, 'relancée'));

               DB::commit();
               return response()->json(['success' => 'La demande a été relancée et est maintenant en attente.'], 200);
           } catch (\Exception $e) {
               DB::rollBack();
               return response()->json(['error' => $e->getMessage()], 400);
           }
       }

       public function getNotificationsForRoleDeux()
       {
           $authenticatedUser = Auth::user();

           if ($authenticatedUser->role_id != 2) {
               return response()->json(['error' => 'Unauthorized: Only users with role_id 2 can view notifications.'], 403);
           }

           // Retrieve notifications for the user
           $notifications = $authenticatedUser->notifications; // Assuming notifications are stored in the User model

           return response()->json([
               'notifications' => $notifications,
               'statut' => 200,
               'message' => 'Notifications récupérées'
           ]);
       }

}
