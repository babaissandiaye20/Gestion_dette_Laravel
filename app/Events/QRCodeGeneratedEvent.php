<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QRCodeGeneratedEvent
{
    use Dispatchable, SerializesModels;

    public $client;

    public function __construct($client)
    {
        $this->client = $client;
    }
}
