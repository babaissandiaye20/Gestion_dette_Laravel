<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ClientRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'client_repository';
    }
}
