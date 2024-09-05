<?php



namespace App\Services;

interface UploadService
{
    /**
     * Télécharge un fichier dans un répertoire spécifié.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return mixed
     */
    public function upload(\Illuminate\Http\UploadedFile $file, string $directory);

    /**
     * Récupère le contenu du fichier en Base64.
     *
     * @param string $filePath
     * @return string
     */
    public function getBase64(string $filePath): string;
}