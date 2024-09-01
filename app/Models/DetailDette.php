<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailDette extends Model
{
    // Spécifiez la table associée si elle ne suit pas la convention de nommage
    protected $table = 'detail_dettes';

    // Les attributs que vous pouvez remplir massivement
    protected $fillable = [
        'dette_id',
        'article',
        'quantite',
        'prix',
    ];

    // Définir la relation avec le modèle `Dette`
    public function dette()
    {
        return $this->belongsTo(Dette::class, 'dette_id');
    }
}
