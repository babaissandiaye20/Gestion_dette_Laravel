<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomTokenService;
use Laravel\Passport\PersonalAccessTokenFactory;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryImpl;
use App\Repositories\ClientRepository;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Services\ClientService;
use  App\Services\ClientServiceInterface;
use App\Repositories\ClientRepositoryInterface;
use App\Services\QRCodeService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(QRCodeService::class, function ($app) {
            return new QRCodeService();
        });
        $this->app->singleton(CustomTokenService::class, function ($app) {
            return new CustomTokenService($app->make(PersonalAccessTokenFactory::class));
        });
        $this->app->bind(ArticleRepository::class, ArticleRepositoryImpl::class);
        $this->app->bind(ArticleService::class, ArticleServiceImpl::class);
        $this->app->singleton('client_repository', function ($app) {
            return new ClientRepository($app->make(QRCodeService::class));
        });
        
        // Vous pouvez également enregistrer le ClientService de la même manière si nécessaire
        /* $this->app->singleton('client_service', function ($app) {
            return new ClientService($app->make('client_repository'));
        });*/
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class); 
        $this->app->singleton(ClientServiceInterface::class, ClientService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
