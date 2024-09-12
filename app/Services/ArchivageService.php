<?php

namespace App\Services;

use App\Services\MongoDBService;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class ArchivageService
{
    protected $service;

    public function __construct(FirebaseService $firebaseService, MongoDBService $mongoDBService)
    {
        $serviceType = env('ARCHIVAGE_SERVICE', 'firebase');

        if ($serviceType === 'mongodb') {
            $this->service = $mongoDBService;
        } else {
            $this->service = $firebaseService;
        }

    }

    public function store($data)
    {
        // Log the data to check what is being stored

        return $this->service->store($data);
    }
}
