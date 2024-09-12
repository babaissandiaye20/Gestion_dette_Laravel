<?php

namespace App\Repositories;

use App\Repositories\PaiementRepositoryInterface;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Collection;

class PaiementRepository implements PaiementRepositoryInterface
{
    public function create(array $data): Paiement
    {
        return Paiement::create($data);
    }

    public function getPaymentsByDebtId(int $detteId): Collection
    {
        return Paiement::where('dette_id', $detteId)->get();
    }

    public function getPaymentsByDate(string $date): Collection
    {
        return Paiement::whereDate('created_at', $date)->get();
    }

    public function deletePayment(Paiement $paiement): void
    {
        $paiement->delete();
    }
}
