<?php
namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\SendFidelityCardEmailJob;

class SendFidelityCardEmail
{
    public function handle(ClientCreated $event)
    {
        // Dispatch a job to send the fidelity card email
        SendFidelityCardEmailJob::dispatch($event->client);
    }
}
