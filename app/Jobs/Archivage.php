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
        // Resolve the ArchivageService dependency within the handle method
        $archivageService = app(ArchivageService::class);


        $clientDette = ClientRepositoryFacade::getClientWithDebtswithArticle();
        $archivageService->store($clientDette);

        Log::debug('Clients archivés avec succès');
    }
}
