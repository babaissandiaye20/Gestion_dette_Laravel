<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\PaiementObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
#[ObservedBy([PaiementObserver::class])]
class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'dette_id',
        'montant',
    ];

    // Relation avec Dette
    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
}