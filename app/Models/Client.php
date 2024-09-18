<?php

namespace App\Models;
use App\Observers\ClientObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use App\Enums\ClientCategory;
#[ObservedBy([ClientObserver::class])]

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['telephone', 'surnom', 'adresse', 'user_id', 'category', 'max_debt_amount'];

    protected $hidden=['created_at','updated_at'];
    protected $appends = ['photo'];


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
    public function getPhotoAttribute()
    {
        // Si un utilisateur est associé, ne pas afficher l'attribut 'photo'
        if (!is_null($this->user_id)) {
            return null; // Pas d'attribut 'photo' pour les clients ayant un user_id
        }

        // Retourner la valeur de la photo par défaut si aucun utilisateur n'est associé
        return 'public/storage/photos/0ZkPKVIGxRqPtrcEt7BGZleR3nq6nLVKXB2CVCPh.jpg';  // Remplacez par le chemin de la photo par défaut
    }

    // Supprimer l'événement retrieved
    protected static function boot()
    {
        parent::boot();
        // On peut enlever l'événement retrieved car l'accessoire fait le travail
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
 public function routeNotificationForSms()
    {
        return $this->telephone;


}
public function getCategoryAttribute($value)
    {
        // Convertir la valeur en énumération lorsque l'attribut est accédé
        return ClientCategory::from($value);
    }

    public function setCategoryAttribute($value)
    {
        // Convertir la valeur en entier avant de la stocker
        $this->attributes['category'] = $value instanceof ClientCategory ? $value->value : $value;
    }
public function getTotalPaiementsAttribute()
{
    // Sum all the payments associated with this client's debts
    return $this->dettes()->withSum('paiements', 'montant')->get()->sum('paiements_sum_montant');
}


}
