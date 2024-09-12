<?php

namespace App\Providers;

use App\Services\UserService;
use App\Services\DetteService;
use App\Services\MongoService;
use App\Services\ClientService;
use App\Services\QRCodeService;
use App\Services\UploadService;
use App\Services\ArticleService;

use App\Services\PaiementService;
use App\Services\ArchivageService;
use App\Services\DetteServiceImpl;
use App\Services\TwilioSmsService;
use App\Repository\DetteRepository;
use App\Services\InfobipSmsService;
use App\Services\UploadServiceImpl;
use App\Facades\ClientServiceFacade;
use App\Repositories\UserRepository;
use App\Services\ArticleServiceImpl;
use App\Services\CustomTokenService;
use App\Services\FidelityCardService;
use App\Services\PhotoStorageService;
use App\Services\SmsServiceInterface;
use App\Repositories\ClientRepository;
use App\Services\UserServiceInterface;
use App\Facades\ClientRepositoryFacade;
use App\Repositories\ArticleRepository;
use App\Repositories\DetteRepositories;
use App\Repository\DetteRepositoryImpl;
use Illuminate\Support\ServiceProvider;
use App\Repositories\PaiementRepository;
use  App\Services\ClientServiceInterface;
use App\Services\PaiementServiceInterface;
use App\Repositories\ArticleRepositoryImpl;
use App\Repositories\DetteRepositoriesImpl;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use Laravel\Passport\PersonalAccessTokenFactory;
use App\Repositories\PaiementRepositoryInterface;

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
        $this->app->singleton('imageUploadService', function ($app) {
            return new UploadServiceImpl;
        });
          // Enregistrer FidelityCardService
          $this->app->singleton(FidelityCardService::class, function ($app) {
            return new FidelityCardService($app->make(QRCodeService::class));
        });
        $this->app->singleton(CustomTokenService::class, function ($app) {
            return new CustomTokenService($app->make(PersonalAccessTokenFactory::class));
        });
        $this->app->bind(ArticleRepository::class, ArticleRepositoryImpl::class);
        $this->app->bind(ArticleService::class, ArticleServiceImpl::class);
        $this->app->singleton('client_repository', function ($app){
            return $app->make(ClientRepository::class);
        });
        
        $this->app->singleton('clientservice', function ($app) {
            return new ClientServiceFacade(
                $app->make(QRCodeService::class),
                $app->make(FidelityCardService::class),
                $app->make(PhotoStorageService::class)
            );
        });
       
        
        // Vous pouvez également enregistrer le ClientService de la même manière si nécessaire
        /* $this->app->singleton('client_service', function ($app) {
            return new ClientService($app->make('client_repository'));
        });*/
        $this->app->singleton(ClientServiceInterface::class, ClientService::class);
        $this->app->singleton('clientservice', function ($app) {
            return $app->make(ClientService::class);
        });
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class); 
        $this->app->singleton(ClientServiceInterface::class, ClientService::class);

        $this->app->bind(DetteRepositories::class, DetteRepositoriesImpl::class);
        
       
          $this->app->bind(DetteService::class, DetteServiceImpl::class);
          $this->app->bind(PaiementServiceInterface::class, PaiementService::class);
        $this->app->bind(PaiementRepositoryInterface::class, PaiementRepository::class);
       
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
     
        $this->app->bind(SmsServiceInterface::class, function () {
            if (env('SMS_PROVIDER') === 'twilio') {
                return new TwilioSmsService();
            } else {
                return new InfobipSmsService();
            }
        });
        
    }




    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
