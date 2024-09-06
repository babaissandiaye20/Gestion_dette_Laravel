<?php 
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhotoUploadedEvent
{
    use Dispatchable, SerializesModels;

    public $photo;

    public function __construct($photo)
    {
        $this->photo = $photo;
    }
}
