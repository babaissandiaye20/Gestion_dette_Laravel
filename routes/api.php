<?php

use App\Models\Dette;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ArchivageController;
// Endpoint pour le login
Route::post('/login', [UserController::class, 'login']);
Route::post('/archive-clients-test', [ArchivageController::class, 'archiveClients']);
 // Affiche tous les clients
 // Crée un nouvel utilisateur
 Route::get('dettes/{id}', [DetteController::class, 'getDette']);

 Route::get('/archive-clients', [ClientController::class, 'archiveClients']);

 Route::post('/clients/archive-to-mongo', [ClientController::class, 'archiveClientsToMongo']);
// Route pour créer une nouvelle dette
Route::post('dettes', [DetteController::class, 'createDette']);

Route::get('/send-sms-to-clients', [SmsController::class, 'sendSmsToClients']);
// Route pour ajouter des articles à une dette spécifique
Route::post('dettes/{id}/articles', [DetteController::class, 'addArticlesToDette']);

//pour payment 
Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');

// Route pour afficher les paiements par dette (par dette ID)
Route::get('/paiements/dette/{detteId}', [PaiementController::class, 'showPaymentsByDebt'])->name('paiements.showByDebt');

// Route pour afficher les paiements par date
Route::get('/paiements/date/{date}', [PaiementController::class, 'showPaymentsByDate'])->name('paiements.showByDate');

// Route pour supprimer un paiement
Route::delete('/paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
Route::post('/fire', [FirebaseController::class, 'send']);


// Route pour récupérer toutes les dettes avec un filtre optionnel par statut
Route::get('dettes', [DetteController::class, 'getAllDettes']);
Route::middleware('auth:api')->group(function () {
    // Clients Routes
    Route::get('/clients/{id}', [ClientController::class, 'getClientById']); // Affiche les détails d'un client par ID
    Route::post('/clients', [ClientController::class, 'register']);// Inscrit un nouveau client
    Route::post('/client', [ClientController::class, 'create']); // Crée un client (duplicata ?)
    Route::post('/clients/telephone', [ClientController::class, 'getClientsByTelephones']); // Recherche des clients par numéro de téléphone
    Route::get('/clients/dettes/{id}', [ClientController::class, 'afficherDettes']); // Affiche les dettes d'un client par ID
    Route::post('/clients/{id}/user', [ClientController::class, 'getClientWithUser']); // Associe un utilisateur à un client
    Route::post('/clients/{id}/dettes', [ClientController::class, 'listDettes']); // Liste les dettes d'un client
    Route::get('/clients', [ClientController::class, 'getClientsWithFilters']);
    // Articles Routes
    Route::post('/articles/libelle', [ArticleController::class, 'searchByLibelle']); // Recherche d'articles par libellé
    Route::post('/articles/update-quantities', [ArticleController::class, 'updateQuantities']); // Met à jour les quantités d'articles
    Route::apiResource('articles', ArticleController::class); // Routes RESTful pour les articles

    // Roles Routes
    Route::post('/roles', [RoleController::class, 'create']); // Crée un nouveau rôle

    // Users Routes
    Route::get('/user', [UserController::class, 'index']); // Affiche la liste des utilisateurs
    Route::post('/users', [UserController::class, 'create']);
    Route::put('/users/{id}', [UserController::class, 'update']); // Met à jour un utilisateur existant
    Route::patch('/users/{id}', [UserController::class, 'updatePartial']); // Mise à jour partielle d'un utilisateur
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Supprime un utilisateur
});
