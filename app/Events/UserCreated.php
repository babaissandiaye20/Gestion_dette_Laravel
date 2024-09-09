<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $photoPath;

    public function __construct(User $user, $photoPath)
    {
        Log::info("UserCreated event fired for user: " . $user->id . ", photoPath: " . $photoPath);
        $this->user = $user;
        $this->photoPath = $photoPath;
    }
}
