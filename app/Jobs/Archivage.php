<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Facades\ClientRepositoryFacade;
use App\Services\ArchivageService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Archivage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Resolve the ArchivageService dependency
        $archivageService = app(ArchivageService::class);
    
        // Vérifier si MongoDB ou Firebase est utilisé
        if (env('ARCHIVAGE_SERVICE') === 'mongodb') {
            // Utiliser la méthode spécifique à MongoDB
            $clientDette = ClientRepositoryFacade::getClientWithDebtswithArticleForMongo();
        } else {
            // Utiliser la méthode originale pour Firebase
            $clientDette = ClientRepositoryFacade::getClientWithDebtswithArticle();
        }
    
        // Stocker les données dans le service approprié (MongoDB ou Firebase)
       /*  $archivageService->store($clientDette); */
        $archivageService->store($clientDette);
        $result = 'Clients archivés ';
       Log::debug( $result);
    }
    
} 
