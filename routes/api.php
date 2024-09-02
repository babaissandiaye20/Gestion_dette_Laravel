<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RoleController;

// Endpoint pour le login
Route::post('/login', [UserController::class, 'login']);

// Routes protégées par middleware d'authentification (nécessitent un token valide)
Route::middleware('auth:api')->group(function () {

    // Clients Routes
    Route::get('/clients', [ClientController::class, 'indexbis']); // Affiche tous les clients
    Route::get('/clients/{id}', [ClientController::class, 'getClientById']); // Affiche les détails d'un client par ID
    Route::post('/clients', [ClientController::class, 'register'])->middleware('custom.unauthorized'); // Inscrit un nouveau client
    Route::post('/client', [ClientController::class, 'create'])->middleware('custom.unauthorized'); // Crée un client (duplicata ?)
    Route::post('/clients/telephone', [ClientController::class, 'getClientsByTelephones']); // Recherche des clients par numéro de téléphone
    Route::get('/clients/dettes/{id}', [ClientController::class, 'afficherDettes']); // Affiche les dettes d'un client par ID
    Route::post('/clients/{id}/user', [ClientController::class, 'getClientWithUser']); // Associe un utilisateur à un client
    Route::post('/clients/{id}/dettes', [ClientController::class, 'listDettes']); // Liste les dettes d'un client

    // Articles Routes
    Route::post('/articles/libelle', [ArticleController::class, 'searchByLibelle']); // Recherche d'articles par libellé
    Route::post('/articles/update-quantities', [ArticleController::class, 'updateQuantities']); // Met à jour les quantités d'articles
    Route::apiResource('articles', ArticleController::class); // Routes RESTful pour les articles

    // Roles Routes
    Route::post('/roles', [RoleController::class, 'create']); // Crée un nouveau rôle

    // Users Routes
    Route::get('/user', [UserController::class, 'index']); // Affiche la liste des utilisateurs
    Route::post('/users', [UserController::class, 'create']); // Crée un nouvel utilisateur
    Route::put('/users/{id}', [UserController::class, 'update']); // Met à jour un utilisateur existant
    Route::patch('/users/{id}', [UserController::class, 'updatePartial']); // Mise à jour partielle d'un utilisateur
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Supprime un utilisateur
});
