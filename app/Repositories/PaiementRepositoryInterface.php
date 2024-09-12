<?php

namespace App\Repositories;

use App\Models\Paiement;
use Illuminate\Database\Eloquent\Collection;

interface PaiementRepositoryInterface
{
    public function create(array $data): Paiement;
    public function getPaymentsByDebtId(int $detteId): Collection;
    public function getPaymentsByDate(string $date): Collection;
    public function deletePayment(Paiement $paiement): void;
}
