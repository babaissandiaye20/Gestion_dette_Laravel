<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $table = 'demandes'; // Nom de la table dans la base de données
    protected $fillable = ['client_id', 'montant', 'articles', 'statut']; // Champs que vous pouvez remplir

    // Ajoutez des relations si nécessaire
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
public function user()
    {
        return $this->belongsTo(User::class, 'user_id');  // Assurez-vous que 'user_id' est la clé étrangère dans la table 'demandes'
    }

}
