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

        $newData = $this->database->getReference(date('Y-m-d'))->push($request);
        return response()->json($newData->getValue());
    }
  public function retrieve($id = null, $date = null)
  {
      // Récupération des données depuis Firebase
      $reference = $this->database->getReference('/');
      $data = $reference->getValue();

      // Débogage pour voir les données récupérées
     // dd($data);

      return $data ? array_values($data) : [];
  }




}
