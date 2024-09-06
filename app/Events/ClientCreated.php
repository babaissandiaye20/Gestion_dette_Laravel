<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientCreated
{
    use Dispatchable, SerializesModels;

    public $client;
    public $photoPath;

    public function __construct(Client $client, $photoPath = null)
    {
        $this->client = $client;
        $this->photoPath = $photoPath;  // Path to the photo, not the file object
    }
}
