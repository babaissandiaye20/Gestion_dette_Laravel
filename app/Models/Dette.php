<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'montant','status'];
    protected $hidden=['id','created_at','updated_at'];
    protected $appends = ['total_paiements'];
    public function client()
    {
        // Notez que "Client" doit être avec un "C" majuscule
        return $this->belongsTo(Client::class);
    }

    // Relation avec Paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'detail_dettes')
                    ->withPivot('qte_vente', 'prix_vente')
                    ->withTimestamps();
    }
      // Scope local pour filtrer les dettes soldées ou non soldées
      public function scopeStatut($query, $statut)
    {
        return $query->where(function ($q) use ($statut) {
            if ($statut === 'Solde') {
                $q->where('montant', 0);
            } else {
                $q->where('montant', '>', 0);
            }
        });
    }
   
    public function getTotalPaiementsAttribute()
    {
        // Calculer la somme des paiements pour cette dette
        return $this->paiements()->sum('montant');
    }

    public function setStatus()
    {
        $montantDette = (float) $this->montant; // Convertir en flottant
        $totalPaiements = (float) $this->total_paiements; // Somme des paiements

        // Vérifier si le montant de la dette est égal à la somme des paiements
        if ($totalPaiements >= $montantDette) {
            $this->status = 'settled'; // Dette réglée
        } else {
            $this->status = 'encours'; // Dette en cours
        }

        $this->save();
    }

}
      
