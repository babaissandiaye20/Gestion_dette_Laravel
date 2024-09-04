<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Gérer l'upload d'un fichier.
     *
     * @param UploadedFile $file Le fichier à uploader.
     * @param string $directory Le répertoire où stocker le fichier.
     * @param string|null $disk Le nom du disque de stockage (par défaut 'public').
     * @return string Le chemin du fichier uploadé.
     */
    public function uploadFile(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }
}
