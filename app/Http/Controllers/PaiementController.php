<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\PaiementServiceInterface;
use App\Http\Requests\StorePaiementRequest;

class PaiementController extends Controller

{
    protected $paiementService;

    public function __construct(PaiementServiceInterface $paiementService)
    {
        $this->paiementService = $paiementService;
    }

    public function store(StorePaiementRequest $request)
    {
        try {
            $paiement = $this->paiementService->createPaiement($request->dette_id, $request->montant);
            return response()->json(['message' => 'Payment created successfully.', 'paiement' => $paiement], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function showPaymentsByDebt($detteId)
    {
        $paiements = $this->paiementService->getPaymentsForDebt($detteId);
        return response()->json($paiements);
    }

    public function showPaymentsByDate($date)
    {
        $paiements = $this->paiementService->getPaymentsByDate($date);
        return response()->json($paiements);
    }

    public function destroy(Paiement $paiement)
    {
        try {
            $this->paiementService->deletePayment($paiement);
            return response()->json(['message' => 'Payment deleted and debt amount updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
