<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ImageUploadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imageUploadService';
    }
}