<?php

namespace App\Models;

// J'importe ici les classes nécessaires pour gérer le modèle Like
// et utiliser les relations ORM avec Laravel.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    // Ici je précise les champs qu'on peut remplir automatiquement
    // quand un utilisateur like un article.
    protected $fillable = [
        'user_id',
        'article_id',
    ];

    // Cette fonction permet de récupérer l'utilisateur qui a mis le like.
    // Un like appartient à un seul utilisateur(One to One).
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cette fonction permet de récupérer l'article qui a été liké.
    // Un like appartient aussi à un seul article(One to One).
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}