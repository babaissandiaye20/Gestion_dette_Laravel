<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PhotoStorageService;
use Illuminate\Support\Facades\Log;

class UploadUserPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $photoPath;

    public function __construct(User $user, $photoPath)
    {
        $this->user = $user;
        $this->photoPath = $photoPath;
    }

    public function handle(PhotoStorageService $photoStorageService)
    {
        Log::info("Handling photo upload for user: " . $this->user->id);
    
        if ($this->photoPath === null) {
            Log::error("Photo path is null for user: " . $this->user->id);
            return;
        }
    
        // Conversion du chemin relatif en chemin absolu
        $absolutePhotoPath = storage_path('app/public/' . $this->photoPath);
    
        if (!file_exists($absolutePhotoPath)) {
            Log::error("File does not exist at path: " . $absolutePhotoPath);
            return;
        }
    
        try {
            // Upload de la photo vers Cloudinary avec le chemin du fichier
            $photoUrl = $photoStorageService->uploadPhoto($absolutePhotoPath);
    
            if ($photoUrl) {
                // Mise à jour de l'utilisateur avec l'URL de la photo Cloudinary
                $this->user->photo = $photoUrl;
                $this->user->save();
                Log::info("Photo URL successfully updated for user: " . $this->user->id);
            }
        } catch (\Exception $e) {
            Log::error("Failed to upload photo for user: " . $this->user->id . ". Error: " . $e->getMessage());
        }
    }
    
}
