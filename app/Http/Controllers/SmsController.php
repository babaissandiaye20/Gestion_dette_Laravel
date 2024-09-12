<?php
namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // Endpoint pour tester l'envoi des SMS
    public function sendSmsToClients()
    {
        $this->smsService->notifyClientsWithDebts();

        return response()->json(['message' => 'SMS envoyés aux clients avec dettes.']);
    }
}
