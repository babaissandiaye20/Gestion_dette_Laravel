<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MongoDBServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MongoDBService';
    }
}
