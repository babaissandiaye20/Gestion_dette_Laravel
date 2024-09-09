<?php

namespace App\Observers;

use App\Models\client;
use App\Events\ClientFidelityEvent;


class ClientObserver
{
    /**
     * Handle the client "created" event.
     */
    public function created(client $client): void
    {
        event(new ClientFidelityEvent($client)); 

    }

    /**
     * Handle the client "updated" event.
     */
    public function updated(client $client): void
    {
        event(new ClientFidelityEvent($client)); 
    }

    /**
     * Handle the client "deleted" event.
     */
    public function deleted(client $client): void
    {
        //
    }

    /**
     * Handle the client "restored" event.
     */
    public function restored(client $client): void
    {
        //
    }

    /**
     * Handle the client "force deleted" event.
     */
    public function forceDeleted(client $client): void
    {
        //
    }
}
