<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use App\Services\PhotoStorageService;

class UploadPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $photoPath;

    public function __construct(Client $client, $photoPath)
    {
        $this->client = $client;
        $this->photoPath = $photoPath; // Conservez le chemin du fichier ici
    }

    public function handle(PhotoStorageService $photoStorageService)
    {
        // Upload the photo using the stored file path
        $photoUrl = $photoStorageService->uploadPhoto($this->photoPath);
    
        // Update the user with the photo URL
        $this->client->user->photo = $photoUrl;
        $this->client->user->save();
    }
    
    
}
