<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'prix', 'qutestock'];
    protected $hidden=['created_at','updated_at','etat'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'articles_users');
    }

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'detail_dettes')->withPivot('qteVente', 'prixVente');
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

    // Scope pour le libellé
    public function scopeLibelle($query, $libelle)
    {
        if (!empty($libelle)) {
            return $query->where('libelle', 'like', '%' . $libelle . '%');
        }

        return $query;
    }
}

