<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleDette extends Pivot
{
    use HasFactory;

    protected $table = 'articles_dettes';

    protected $fillable = ['article_id', 'dette_id', 'qteVente', 'prixVente'];
}

