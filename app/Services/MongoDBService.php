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
        // Insert data into MongoDB and log the result
        $insertResult = $this->collection->insertOne($data);
    
        Log::debug('MongoDB insert result: ', ['inserted_count' => $insertResult->getInsertedCount()]);
    
        if ($insertResult->getInsertedCount() > 0) {
            return response()->json(['success' => true, 'id' => $insertResult->getInsertedId()], 201);
        } else {
            return response()->json(['success' => false], 500);
        }
    }
}
