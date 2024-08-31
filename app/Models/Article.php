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
}

