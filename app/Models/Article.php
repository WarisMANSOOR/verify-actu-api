<?php

namespace App\Models;

// J'importe ici les classes nécessaires pour gérer le modèle Article
// et les relations ORM avec les autres tables.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    // Ici je précise les champs qu'on peut remplir automatiquement
    // quand on crée ou modifie un article avec Laravel.
    protected $fillable = [
        'journalist_id',
        'title',
        'content',
        'status',
        'reliability_score',
        'validated_by',
        'deleted_by',
        'restored_by',
    ];

    // Cette fonction permet de récupérer le journaliste qui a créé l'article.
    // Sans oublier que plusieurs articles peuvent appartenir au même journaliste.
    public function journalist()
    {
        return $this->belongsTo(User::class, 'journalist_id');
    }

    // Cette fonction permet de savoir quel modérateur a validé l'article.
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Cette fonction permet de savoir quel modérateur a supprimé l'article.
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Cette fonction permet de savoir quel modérateur a restauré l'article.
    public function restorer()
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    // Cette fonction permet de récupérer tous les likes liés à un article.
    // et aussi un article peut avoir plusieurs likes.
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Cette fonction représente les utilisateurs qui ont liké cet article.
    // donc elle montre la relation Many-to-Many entre Article et User.
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }
}