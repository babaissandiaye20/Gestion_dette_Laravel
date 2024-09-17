<?php

namespace App\Services;

use Illuminate\Support\Env;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class MongoDBService
{
    protected $client;
    protected $database;
    protected $collection;

    public function __construct()
    {


      /*   // Use dd() to dump the values for immediate debugging and halt execution
        dd([
            'connection_uri' => env('mongodbconnection_uri'),
            'database_name' => env('mongodb.database_name'),
            'collection_name' => env('mongodb.collection_name')
        ]); */

        // Initialize the MongoDB client and select the database and collection
        $this->client = new MongoClient(env('mongodbconnection_uri'));
        $this->database = $this->client->selectDatabase(env('MONGO_DATABASE_NAME'));
        $this->collection = $this->database->selectCollection(env('mongodb_collection_name'));
    }

    public function store($data)
{
    Log::debug('Données avant insertion dans MongoDB : ' . json_encode($data));

    $timestampKey = date('Y-m-d H:i:s');
    $formattedData = [
        'timestamp' => $timestampKey,
        'data' => $data
    ];

    Log::debug('Données formatées pour MongoDB : ' . json_encode($formattedData));

    $insertResult = $this->collection->insertOne($formattedData);

    Log::debug('Résultat de l\'insertion MongoDB : ' . json_encode([
        'inserted_id' => $insertResult->getInsertedId(),
        'count' => $insertResult->getInsertedCount()
    ]));

    if ($insertResult->getInsertedCount() > 0) {
        return ['success' => true, 'data' => $formattedData, 'id' => $insertResult->getInsertedId()];
    } else {
        return ['success' => false];
    }
}


public function retrieve($id = null, $date = null)
{
    // Récupération des données MongoDB sans filtres
    $data = $this->collection->find()->toArray();

    // Débogage pour voir les données récupérées
   // dd($data);

    return $data ? array_values($data) : [];
}



}
