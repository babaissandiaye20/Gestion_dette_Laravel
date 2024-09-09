<?php
namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

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
     * Upload photo to Cloudinary or fallback to original URL.
     *
     * @param string $photoPath
     * @return string|null
     */
    public function uploadPhoto($photoPath)
    {
        try {
            // Vérifier si $photoPath est une URL valide
            if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
                Log::info("Téléchargement de l'image à partir de l'URL : " . $photoPath);

                // Télécharger l'image à partir de l'URL distante
                $imageContents = @file_get_contents($photoPath);

                if ($imageContents === false) {
                    Log::error("Échec du téléchargement de l'image à partir de l'URL : " . $photoPath);
                    return $this->fallbackToOriginalUrl($photoPath);
                }

                // Sauvegarder temporairement le fichier téléchargé
                $tempFilePath = tempnam(sys_get_temp_dir(), 'photo_');
                file_put_contents($tempFilePath, $imageContents);

                // Utiliser le fichier temporaire pour le chargement
                $photoPath = $tempFilePath;
            }

            Log::info("Chemin de la photo avant le chargement : " . $photoPath);

            // Vérifier si le chemin de la photo existe
            if (!file_exists($photoPath)) {
                Log::error("Le chemin de la photo n'existe pas : " . $photoPath);
                return $this->fallbackToOriginalUrl($photoPath);
            }

            // Télécharger la photo sur Cloudinary
            $uploadResult = $this->cloudinary->uploadApi()->upload($photoPath, [
                'folder' => 'clients'
            ]);

            // Journaliser la réponse de Cloudinary
            Log::info('Réponse du chargement sur Cloudinary : ' . json_encode($uploadResult));

            // Vérifier si le téléchargement est réussi
            if (isset($uploadResult['secure_url']) && !empty($uploadResult['secure_url'])) {
                Log::info("Téléchargement réussi sur Cloudinary, URL sécurisée : " . $uploadResult['secure_url']);
                return $uploadResult['secure_url'];
            } else {
                Log::error('Structure inattendue de la réponse Cloudinary : ' . json_encode($uploadResult));
            }
        } catch (\Exception $e) {
            Log::error('Échec du chargement sur Cloudinary : ' . $e->getMessage());
        }

        return $this->fallbackToOriginalUrl($photoPath);
    }

    /**
     * Fallback method to return the original URL of the photo.
     *
     * @param string $photoPath
     * @return string
     */
    private function fallbackToOriginalUrl($photoPath)
    {
        Log::info("Utilisation de l'URL de secours pour la photo : " . $photoPath);
        return $photoPath; // Return the original URL of the photo
    }
}
