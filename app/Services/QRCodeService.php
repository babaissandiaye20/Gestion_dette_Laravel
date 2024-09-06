<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeService
{
    public function generateQRCode(string $qrContent, string $qrCodePath): void
    {
        // Générer le QR code avec endroid/qr-code
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->size(100)
            ->margin(10)
            ->build();

        // Sauvegarder le fichier QR code
        $qrCode->saveToFile(public_path($qrCodePath));
    }
    public function generateQRCodeForClient($client)
    {
        $qrContent = "ID Client: " . $client->id . "\n" .
                     "Nom: " . ($client->user->nom ?? 'N/A') . "\n" .
                     "Prénom: " . ($client->user->prenom ?? 'N/A') . "\n" .
                     "Téléphone: " . ($client->telephone ?? 'N/A') . "\n" .
                     "Surnom: " . ($client->surnom ?? 'N/A');
        
        $qrCodePath = 'qrcodes/client_' . $client->id . '.png';
        $this->generateQRCode($qrContent, $qrCodePath);
        
        return $qrCodePath;
    }
}
