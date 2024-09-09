<?php

namespace App\Jobs;

use App\Mail\FidelityCardMail;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\QRCodeService;
use App\Services\FidelityCardService;
use Illuminate\Support\Facades\Log;
class SendFidelityCardEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $client;
    protected $qrCodeService;
    protected $fidelityCard;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->qrCodeService = new QRCodeService();
        $this->fidelityCard = new FidelityCardService();
    }

    public function handle()
    {
      $qrcodes=$this->qrCodeService->generateQRCodeForClient($this->client);
      $fidelityCardPath = $this->fidelityCard->generateFidelityCardForClient($this->client,$qrcodes);
         // Log::info(  "CLients: "  .$this->client);
        // Envoyer l'email
        Mail::to($this->client->user->login)->send(new FidelityCardMail($this->client, $fidelityCardPath));

    }
}
