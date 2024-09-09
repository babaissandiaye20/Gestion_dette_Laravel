<?php
namespace App\Listeners;

use App\Events\ClientFidelityEvent;
use App\Jobs\SendFidelityCardEmailJob;

class SendFidelityCardEmailListener
{
    public function handle(ClientFidelityEvent $event)
    {
        SendFidelityCardEmailJob::dispatch($event->client);
    }
}
