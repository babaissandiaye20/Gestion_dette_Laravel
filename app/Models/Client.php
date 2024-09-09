<?php

namespace App\Models;

use App\Observers\ClientObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
#[ObservedBy([ClientObserver::class])]

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['telephone', 'surnom', 'adresse', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }
    public function scopeWithUsers(Builder $query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeWithoutUsers(Builder $query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeActive(Builder $query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('etat', 'actif');
        });
    }

    public function scopeInactive(Builder $query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('etat', 'inactif');
        });
    }
}
 