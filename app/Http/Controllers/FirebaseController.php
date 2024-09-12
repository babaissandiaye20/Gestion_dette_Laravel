<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Routing\Controller;

class FirebaseController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function send(Request $request)
    {
        try {
            $this->firebaseService->store($request->all());
            return response()->json(['message' => 'Data successfully pushed to Firebase']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
