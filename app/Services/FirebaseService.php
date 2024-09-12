<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials_file'))
            ->withDatabaseUri(config('firebase.database_url'));  // Use the database URL from the config

        $this->database = $factory->createDatabase();
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function store($request)
    {
        $newData = $this->database->getReference(date('Y-m-d H:i:s'))->push($request);
        return response()->json($newData->getValue());
    }
}
