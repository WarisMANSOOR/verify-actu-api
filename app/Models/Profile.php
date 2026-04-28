<?php

namespace App\Models;

// J'importe ici les classes nécessaires pour gérer le modèle Profile
// et utiliser les relations ORM avec Laravel.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    // Ici je précise les champs qu'on peut remplir automatiquement
    // quand on crée ou modifie un profil.
    protected $fillable = [
        'user_id',
        'bio',
        'phone',
    ];

    // Cette fonction permet de récupérer l'utilisateur lié au profil.
    // Un profil appartient à un seul utilisateur (One to One).
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}