<?php

namespace App\Services;

use Exception;
use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use App\Services\PaiementServiceInterface;
use App\Repositories\PaiementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaiementService implements PaiementServiceInterface
{
    protected $paiementRepository;

    public function __construct(PaiementRepositoryInterface $paiementRepository)
    {
        $this->paiementRepository = $paiementRepository;
    }

    public function createPaiement(int $detteId, float $montant): Paiement
    {
        DB::beginTransaction(); // Start the transaction

        try {
            $dette = Dette::findOrFail($detteId);

            if ($dette->montant <= 0) {
                throw new Exception("This debt is already paid off and no more payments can be made.");
            }

            if ($montant > $dette->montant) {
                throw new Exception("The payment amount cannot be greater than the debt amount.");
            }

            // Create the payment
            $paiement = $this->paiementRepository->create([
                'dette_id' => $detteId,
                'montant' => $montant,
            ]);

            // Decrease the debt amount
           /*  $dette->montant -= $montant;
            $dette->save(); */

            DB::commit(); // Commit the transaction

            return $paiement;
        } catch (Exception $e) {
            DB::rollBack(); // Roll back the transaction

            // Handle the exception (e.g., log the error, rethrow, return a response, etc.)
            throw new Exception("Payment creation failed: " . $e->getMessage());
        }
    }

    public function getPaymentsForDebt(int $detteId): Collection
    {
        return $this->paiementRepository->getPaymentsByDebtId($detteId);
    }

    public function getPaymentsByDate(string $date): Collection
    {
        return $this->paiementRepository->getPaymentsByDate($date);
    }

    public function deletePayment(Paiement $paiement): void
    {
        $dette = $paiement->dette;

        // Increase the debt amount
        $dette->montant += $paiement->montant;
        $dette->save();

        $this->paiementRepository->deletePayment($paiement);
    }
}
