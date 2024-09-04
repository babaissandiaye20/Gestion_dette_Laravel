<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'prix', 'qutestock'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'articles_users');
    }

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'articles_dettes')->withPivot('qteVente', 'prixVente');
    }
    public function scopeDisponible($query, $disponible)
    {
        if ($disponible === 'oui') {
            return $query->where('qutestock', '>', 0);
        } elseif ($disponible === 'non') {
            return $query->where('qutestock', '=', 0);
        }

        return $query;
    }
}

