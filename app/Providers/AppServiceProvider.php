<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomTokenService;
use Laravel\Passport\PersonalAccessTokenFactory;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CustomTokenService::class, function ($app) {
            return new CustomTokenService($app->make(PersonalAccessTokenFactory::class));
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
