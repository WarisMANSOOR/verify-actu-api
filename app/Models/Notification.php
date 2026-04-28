<?php

namespace App\Models;

// J'importe ici les classes nécessaires pour gérer le modèle Notification
// et utiliser les relations ORM avec Laravel.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    // Ici je précise les champs qu'on peut remplir automatiquement
    // quand on crée une notification.
    protected $fillable = [
        'user_id',
        'article_id',
        'message',
        'is_read',
    ];

    // Ici je précise que is_read doit être traité comme un booléen.
    // Comme ça Laravel comprend bien true ou false et comme ça on peut montrer ou cacher l'arcticle.
    protected $casts = [
        'is_read' => 'boolean',
    ];

    // Cette fonction permet de récupérer l'utilisateur qui reçoit la notification.
    // Une notification appartient à un seul utilisateur.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cette fonction permet de récupérer l'article lié à la notification.
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}