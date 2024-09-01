<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use Laravel\Passport\Passport;
use App\Http\Controllers\RoleController;
    Route::get('/user', function() {
        return App\Models\User::all();
    });
    Route::get('/user/{id}', function($id) {
        return App\Models\User::find($id);
    });
    Route::post('/users', [UserController::class, 'create']);
    /* Route::post('/clients', [ClientController::class, 'create']); */
    
    Route::get('/clients', [ClientController::class, 'indexbis']);
 Route::post('/articles', [ArticleController::class, 'store']); 
    Route::post('/articles/update-quantities', [ArticleController::class, 'updateQuantities']);
    Route::post('/update-quantities', [ArticleController::class, 'updateQuantities']);
    // Route pour show: afficher un client par ID
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::get('/roles/{name}', [RoleController::class, 'getRoleByName']);
    
   
    /*  Route::apiResource('articles', ArticleController::class)->except(['pdateQuantities']);  */
    
    Route::post('/login', [UserController::class ,'login'])->name('login');
    Route::apiResource('articles', ArticleController::class);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('/clients/telephone', [ClientController::class, 'getClientsByTelephones']);

        Route::get('/clients/{id}', [ClientController::class, 'getClientById']);
    Route::post('/clients/{id}/user', [ClientController::class, 'getClientWithUser']);
    Route::post('/clients/{id}/dettes', [ClientController::class, 'listDettes']);

        Route::post('/roles', [RoleController::class, 'create']);
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'create']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::patch('/users/{id}', [UserController::class, 'updatePartial']); // Si vous avez une méthode patch
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::post('/clients', [ClientController::class, 'register'])->middleware('custom.unauthorized');
        Route::post('/client', [ClientController::class, 'create'])->middleware('custom.unauthorized');
     
    });

