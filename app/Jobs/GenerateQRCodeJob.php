<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\QRCodeService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateQRCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function handle(QRCodeService $qrCodeService)
    {
        $qrContent = "ID Client: " . $this->client->id . "\n" .
                     "Nom: " . ($this->client->user->nom ?? 'N/A') . "\n" .
                     "Prénom: " . ($this->client->user->prenom ?? 'N/A') . "\n" .
                     "Téléphone: " . ($this->client->telephone ?? 'N/A') . "\n" .
                     "Surnom: " . ($this->client->surnom ?? 'N/A');
        $qrCodePath = 'qrcodes/client_' . $this->client->id . '.png';
        $qrCodeService->generateQRCode($qrContent, $qrCodePath);

        return $qrCodePath;
    }
}
