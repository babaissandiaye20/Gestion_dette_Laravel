<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'montant', 'montantDu', 'montantRestant', 'client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'articles_dettes')->withPivot('qteVente', 'prixVente');
    }
    public function details()
    {
        return $this->hasMany(DetailDette::class, 'dette_id');
    }
}

