<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FidelityCardGeneratedEvent
{
    use Dispatchable, SerializesModels;

    public $client;
    public $qrCodePath;

    public function __construct($client, $qrCodePath)
    {
        $this->client = $client;
        $this->qrCodePath = $qrCodePath;
    }
}
