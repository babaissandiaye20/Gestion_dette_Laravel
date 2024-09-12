<?php

namespace App\Http\Controllers;

use App\Jobs\Archivage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ArchivageController extends Controller
{
    /**
     * Dispatch the Archivage job.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function archiveClients()
    {
        // Dispatch the job
        Archivage::dispatch();

        return response()->json(['message' => 'Archivage des clients initié avec succès.'], 200);
    }
}
