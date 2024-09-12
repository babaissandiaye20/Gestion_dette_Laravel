<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MongoDBService;
use App\Services\FirebaseService;
use App\Services\ArchivageService;

class MongoDBServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {  
        // Register FirebaseService with the fully qualified class name
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });

        // Register MongoDBService with the fully qualified class name
        $this->app->singleton(MongoDBService::class, function ($app) {
            return new MongoDBService(); 
        });

        // Register ArchivageService with dependencies
        $this->app->singleton(ArchivageService::class, function ($app) {
            return new ArchivageService(
                $app->make(FirebaseService::class),
                $app->make(MongoDBService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // No bootstrap logic needed here
    }
}
