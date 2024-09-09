<?php
namespace App\Jobs;

use App\Models\User;
use App\Services\PhotoStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UploadUserPhotoToCloudinaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Créer une nouvelle instance du job.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Exécuter le job.
     *
     * @param PhotoStorageService $photoStorageService
     * @return void
     */
    public function handle(PhotoStorageService $photoStorageService)
    {
        Log::info("Starting upload for user: " . $this->user->id);
        
        // Récupérer la photo sous forme de chemin (ou URL)
        $photoPath = $this->user->photo;
        
        if (!$photoPath) {
            Log::error("No photo found for user: " . $this->user->id);
            return;
        }

        // URL de base Cloudinary à vérifier
        $cloudinaryBaseUrl = 'https://res.cloudinary.com/dv3nhosdz/image/upload/';

        // Vérifier si l'URL est déjà celle de Cloudinary
        if (strpos($photoPath, $cloudinaryBaseUrl) === 0) {
            Log::info("Photo already uploaded to Cloudinary for user: " . $this->user->id);
            return;
        }

        // Sinon, tenter l'upload vers Cloudinary
        $photoUrl = $photoStorageService->uploadPhoto($photoPath);

        if ($photoUrl) {
            Log::info("Upload successful for user: " . $this->user->id . ", photo URL: " . $photoUrl);
        
            // Mise à jour de l'utilisateur avec l'URL sécurisée de la photo
            $this->user->photo = $photoUrl; // Use the new Cloudinary URL
            $this->user->type = true; // Marquer la photo comme uploadée
            $this->user->save();
        
            Log::info("User photo updated in database for user: " . $this->user->id);
        } else {
            Log::error("Failed to upload photo for user: " . $this->user->id);
        }
        
    }
}
