<?php

namespace App\Models;

// J'importe ici les classes nécessaires pour gérer l'authentification,
// les tokns API, les notifications Laravel et les relations ORM.
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Ici je précise les champs qu'on a le droit de remplir automatiquement.
    // Ça évite que Laravel bloque la création d'un utilisateur avec User::create().
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // Ici je cache les informations sensibles quand un utilisateur est renvoyé en JSON.
    // comme le mot de passe ne doit jamais apparaître dans une réponse API.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cette fonction montre qu'un utilisateur possède un seul profil.
    // donc c'est la relation One-to-One demandée dans le sujet.
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // Cette fonction montre qu'un journaliste peut proposer plusieurs articles, donc c'est une relation One-to-Many.
    public function articles()
    {
        return $this->hasMany(Article::class, 'journalist_id');
    }

    // Cette fonction permet de récupérer toutes les notifications reçues par un utilisateur.
    // Un utilisateur peut avoir plusieurs notifications.
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Cette fontion permet de récupérer les likes liées à l'utilisateur.
    // Elle sert surtout pour gérer les likes de façon plus directe.
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Cette fontion représente les likes par un utilisateur.
    // Elle montre la relation Many-to-Many entre User et Article.
    public function likedArticles()
    {
        return $this->belongsToMany(Article::class, 'likes')->withTimestamps();
    }
}