<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use App\Mail\FidelityCardMail;
use Illuminate\Support\Facades\Mail;

class SendFidelityCardEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    public function handle()
{
    $qrCodePath = app('App\Services\QRCodeService')->generateQRCodeForClient($this->client);
    
    // Ensure that the client has a user and the user has a photo
    if ($this->client->user && $this->client->user->photo) {
        $photoUrl = $this->client->user->photo;
        $encodedPhoto = $this->encodePhotoToBase64($photoUrl);
    } else {
        // Handle case where no user or no photo exists
        $encodedPhoto = null;  // Or provide a default photo
    }
    
    $fidelityCardPath = app('App\Services\FidelityCardService')->generateFidelityCard($this->client, $qrCodePath, $encodedPhoto);
    
    Mail::to($this->client->user->login)->send(new FidelityCardMail($this->client, $fidelityCardPath));
}
        protected function encodePhotoToBase64($photoUrl)
{
    if ($photoUrl) {
        try {
            $imageData = file_get_contents($photoUrl);
            $imageExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            return 'data:image/' . $imageExtension . ';base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            return null;
        }
    }

    return null;
}

    
}

