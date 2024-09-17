<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
  use Laravel\Passport\HasApiTokens ;
 // or
 /*  use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;  */
class User extends Authenticatable
{
    use   HasApiTokens   /* sanctumHasApiTokens */  ,HasFactory,  Notifiable;


    protected $fillable = ['nom', 'prenom', 'login', 'password', 'role_id','photo'];
    protected $hidden=['created_at','updated_at'];

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class); // Relation One-to-One
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'articles_users');
    }
    public function setPhotoAttribute($value)
    {
        // URL de Cloudinary à vérifier
        $cloudinaryBaseUrl = 'https://res.cloudinary.com/dv3nhosdz/image/upload/';

        // Si la photo est nulle
        if (is_null($value)) {
            $this->attributes['photo'] = null;
            $this->attributes['type'] = false; // Pas d'image (traiter comme une URL non valide)
            return;
        }

        $this->attributes['photo'] = $value;

        // Vérifier si l'URL commence par l'URL de base de Cloudinary
        if (strpos($value, $cloudinaryBaseUrl) === 0) {
            $this->attributes['type'] = true; // C'est une URL Cloudinary
        } else {
            $this->attributes['type'] = false; // Ce n'est pas une URL Cloudinary
        }
    }
public function routeNotificationForSms()
{
    return "+221755263051"; // Numéro de téléphone du client associé
}

}
