<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Facades\MongoDBServiceFacade;
use App\Facades\ClientRepositoryFacade;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MongoArchivage implements ShouldQueue
{
    use Queueable;
    

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $clientDette = ClientRepositoryFacade::getClientWithDebtswithArticle();
         MongoDBServiceFacade::store($clientDette);
        Log::debug('Clients archived successfully in MongoDB');
    }
}
