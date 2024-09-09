<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\UploadUserPhotoJob;
use Illuminate\Support\Facades\Log;

class UploadUserPhotoListener
{
    public function handle(UserCreated $event)
    {
        Log::info("UploadUserPhotoListener received event for user: " . $event->user->id);

        if ($event->photoPath === null) {
            Log::error("Photo path is null in UserCreated event.");
            return;
        }

        // Dispatcher le job pour gérer l'upload de la photo
        UploadUserPhotoJob::dispatch($event->user, $event->photoPath);
    }
}
