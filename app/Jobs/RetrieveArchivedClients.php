<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\ArchivageService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RetrieveArchivedClients implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $archivageService;

    public function __construct(ArchivageService $archivageService)
    {
        $this->archivageService = $archivageService;
    }

    public function handle(): void
    {
        $clients = $this->archivageService->retrieve();

        if (!empty($clients)) {
            Log::info('Clients récupérés avec succès :', ['clients' => $clients]);
        } else {
            Log::info('Aucune donnée trouvée.');
        }
    }
}
