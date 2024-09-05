<?php
namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FidelityCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $fidelityCardPath;

    public function __construct(Client $client, string $fidelityCardPath)
    {
        $this->client = $client;
        $this->fidelityCardPath = $fidelityCardPath;
    }

    public function build()
    {
        return $this->view('emails.fidelity_card')
                    ->subject('Votre carte de fidélité')
                    ->attach($this->fidelityCardPath, [
                        'as' => 'fidelity_card.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
