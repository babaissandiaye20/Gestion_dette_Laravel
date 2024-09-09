<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class CloudinaryUploadService
{
    protected $cloudinary;

    public function __construct()
    {
        Log::info('Initializing Cloudinary upload service');
        
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    /**
     * Upload a base64 photo to Cloudinary and return the secure URL.
     *
     * @param string $base64Photo The base64 encoded photo.
     * @return string|null The secure URL of the uploaded photo, or null if failed.
     */
    public function uploadPhotoToCloudinary($photoPath)
    {
        Log::info('Starting Cloudinary upload for photo');
    
        try {
            // Uploader la photo en utilisant le chemin local ou URL
            $result = $this->cloudinary->uploadApi()->upload($photoPath, [
                'folder' => 'clients',
            ]);
    
            // Log de la réponse complète de Cloudinary
            Log::info('Cloudinary upload result: ', $result);
    
            // Vérifier si la clé 'secure_url' est présente dans la réponse
            if (isset($result['secure_url'])) {
                return $result['secure_url'];
            } else {
                Log::error('No secure_url found in Cloudinary response');
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return null;
        }
    }
    

}

