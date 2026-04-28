<?php

namespace Database\Seeders;

// J'importe les modèles nécessaires pour créer des données de test
// directement dans la base avec Eloquent.
use App\Models\User;
use App\Models\Profile;
use App\Models\Article;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // Cette fonction sert à remplir la base avec des données de test.
    // Comme ça, Mr Samir Youcef pourra tester l'API directement après les migrations.
    public function run(): void
    {
        $waris = User::create([
            'name' => 'Waris',
            'email' => 'waris@test.com',
            'password' => Hash::make('Waris123'),
            'role' => 'utilisateur',
        ]);

        $abir = User::create([
            'name' => 'Abir',
            'email' => 'abir@test.com',
            'password' => Hash::make('Abir123'),
            'role' => 'utilisateur',
        ]);

        $journaliste = User::create([
            'name' => 'Journaliste Test',
            'email' => 'journaliste@test.com',
            'password' => Hash::make('password123'),
            'role' => 'journaliste',
        ]);

        $moderateur = User::create([
            'name' => 'Moderateur Test',
            'email' => 'modo@test.com',
            'password' => Hash::make('password123'),
            'role' => 'moderateur',
        ]);

        // Je crée un profil pour chaque compte pour montrer la relation One-to-One.
        Profile::create([
            'user_id' => $waris->id,
            'phone' => '0600000001',
            'bio' => 'Profil de test pour Waris.',
        ]);

        Profile::create([
            'user_id' => $abir->id,
            'phone' => '0600000002',
            'bio' => 'Profil de test pour Abir.',
        ]);

        Profile::create([
            'user_id' => $journaliste->id,
            'phone' => '0600000003',
            'bio' => 'Profil de test pour un journaliste.',
        ]);

        Profile::create([
            'user_id' => $moderateur->id,
            'phone' => '0600000004',
            'bio' => 'Profil de test pour un modérateur.',
        ]);

        // Cet article est déjà validé pour avoir un exemple visible publiquement.
        $articleValide = Article::create([
            'journalist_id' => $journaliste->id,
            'title' => 'Article validé de démonstration',
            'content' => 'Cet article sert à tester l’affichage public des articles validés.',
            'status' => 'validated',
            'reliability_score' => 85,
            'validated_by' => $moderateur->id,
        ]);

        // Cet article reste en attente pour tester la partie modération.
        Article::create([
            'journalist_id' => $journaliste->id,
            'title' => 'Article en attente de vérification',
            'content' => 'Cet article doit encore être vérifié par un modérateur.',
            'status' => 'pending',
            'reliability_score' => null,
        ]);

        // Je fais liker l'article validé par Waris pour tester le compteur de likes.
        Like::create([
            'user_id' => $waris->id,
            'article_id' => $articleValide->id,
        ]);

        // Je crée aussi une notification de test pour le journaliste.
        Notification::create([
            'user_id' => $journaliste->id,
            'article_id' => $articleValide->id,
            'message' => "Votre article '{$articleValide->title}' a été validé par le modérateur.",
            'is_read' => false,
        ]);
    }
}