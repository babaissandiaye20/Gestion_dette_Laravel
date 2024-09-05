<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Storage;

class PhotoStorageService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    /**
     * Upload photo to Cloudinary or fallback to local Base64 storage.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string|null
     */
    public function uploadPhoto($photo)
    {
        try {
            // Attempt to upload to Cloudinary
            $uploadResult = $this->cloudinary->uploadApi()->upload($photo->getRealPath(), [
                'folder' => 'clients'
            ]);
            return $uploadResult['secure_url']; // Return Cloudinary URL if successful
        } catch (\Exception $e) {
            // Log error and fallback to Base64 storage
         /*    Log::error('Cloudinary upload failed: ' . $e->getMessage()); */
            return $this->storePhotoAsBase64($photo); // Store photo as Base64
        }
    }

    /**
     * Encode the photo to Base64 and store locally.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string
     */
    private function storePhotoAsBase64($photo)
    {
        $photoData = file_get_contents($photo->getRealPath());
        $base64Photo = base64_encode($photoData);
        $base64Url = 'data:image/' . $photo->getClientOriginalExtension() . ';base64,' . $base64Photo;

        // Save Base64 photo to storage (optional if you want to persist it locally)
        $photoPath = 'photos/' . uniqid() . '.txt';
        Storage::disk('local')->put($photoPath, $base64Url);

        return $base64Url; // Return Base64-encoded string
    }
}
