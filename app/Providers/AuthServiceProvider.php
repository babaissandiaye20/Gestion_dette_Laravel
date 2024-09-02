<?php

namespace App\Providers;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\Client;
use App\Policies\ClientPolicy;
use App\Models\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Article::class => ArticlePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Passport::tokensCan([
            'view-posts' => 'View posts',
            'edit-posts' => 'Edit posts',
            // Ajoutez d'autres scopes ici
        ]);
    }
}
