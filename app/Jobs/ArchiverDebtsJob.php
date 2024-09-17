<?php

namespace App\Jobs;

use App\Services\ArchivageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ArchiverDebtsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @param  int|null  $id
     * @param  string|null  $date
     */
    public function __construct($id = null, $date = null)
    {
        $this->id = $id;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ArchivageService $archivageService)
{
    try {
        // Utiliser l'ID 253 si $this->id est null
        $id = $this->id ?? 253;

        // Appel à la méthode archiverDebts du service ArchivageService
        $archivageService->archiverDebts($id, $this->date);
        Log::info('Job ArchiverDebts exécuté avec succès.');
    } catch (\Exception $e) {
        Log::error("Erreur lors de l'exécution du job ArchiverDebts : " . $e->getMessage());
    }
}

}
