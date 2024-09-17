<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VideCache extends Command
{
    // Le nom et la signature de la commande
    protected $signature = 'vide:cache';

    // Description de la commande
    protected $description = 'Vider tous les caches en une fois';

    // Exécuter la commande
    public function handle()
    {
        // Exécuter chaque commande artisan et afficher les messages de retour
        $this->call('schedule:clear-cache');
        $this->info('Schedule cache cleared.');

        $this->call('queue:clear');
        $this->info('Queue cleared.');

        $this->call('event:clear');
        $this->info('Event cache cleared.');
        $this->call('view:clear');
        $this->info('View cache cleared.');

        $this->call('route:clear');
        $this->info('Route cache cleared.');

        $this->call('config:clear');
        $this->info('Config cache cleared.');

        $this->call('cache:clear');
        $this->info('Application cache cleared.');

        $this->info('All caches have been cleared successfully!');
    }
}
