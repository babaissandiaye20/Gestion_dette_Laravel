<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Client;
use App\Models\User;


class ClientRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Client $client, User $user)
    {
        $this->client = $client;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Generate QR code
        $qrCode = QrCode::size(200)->generate("Client ID: {$this->client->id}, Name: {$this->client->surnom}");

        // Generate PDF with the user's photo and QR code
        $pdf = PDF::loadView('emails.client_pdf', [
            'client' => $this->client,
            'user' => $this->user,
            'qrCode' => $qrCode
        ]);

        // Build the email
        return $this->view('emails.client_registered')
            ->subject('Client Registration Details')
            ->attachData($pdf->output(), 'client_details.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
