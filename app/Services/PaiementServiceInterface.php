<?php

namespace App\Services;

use App\Models\Paiement;
use Illuminate\Database\Eloquent\Collection;

interface PaiementServiceInterface
{
    public function createPaiement(int $detteId, float $montant): Paiement;
    public function getPaymentsForDebt(int $detteId): Collection;
    public function getPaymentsByDate(string $date): Collection;
    public function deletePayment(Paiement $paiement): void;
}
