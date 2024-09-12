<?php

return [
    'credentials_file' => env('FIREBASE_CREDENTIALS', base_path('config/firebase_credentials.json')),
    'database_url' => env('FIREBASE_DATABASE_URL', 'https://gestiondettelaravel-default-rtdb.firebaseio.com/')
];
