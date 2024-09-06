<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\UploadPhotoJob;

class UploadClientPhoto
{
    public function handle(ClientCreated $event)
    {
        if ($event->photoPath) {
            // Dispatch the job with the stored photo path
            UploadPhotoJob::dispatch($event->client, $event->photoPath);
        }
    }
}
