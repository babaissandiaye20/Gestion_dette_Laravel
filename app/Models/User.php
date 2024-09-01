<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['nom', 'prenom', 'login', 'password', 'role_id','photo'];

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
}
 