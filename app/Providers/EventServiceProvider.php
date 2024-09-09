<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Events\PhotoUploaded;
use App\Listeners\UploadUserPhotoListener;
use App\Listeners\GenerateFidelityCardListener;
use App\Listeners\GenerateQRCodeListener;
use App\Listeners\SendFidelityCardEmailListener;
use App\Events\ClientFidelityEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserCreated::class => [
            UploadUserPhotoListener::class,
        ],
        ClientFidelityEvent::class => [
            SendFidelityCardEmailListener::class,
        ],
        ];
}
