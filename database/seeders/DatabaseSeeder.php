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
    // Cette fonction sert à remplir la base avec un jeu de données minimal.
    // Le but est de pouvoir tester rapidement les rôles, les articles, les likes et les notifications.
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

        $journaliste1 = User::create([
            'name' => 'Journaliste 1',
            'email' => 'journaliste@test.com',
            'password' => Hash::make('password123'),
            'role' => 'journaliste',
        ]);

        $journaliste2 = User::create([
            'name' => 'Journaliste 2',
            'email' => 'journaliste2@test.com',
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
            'bio' => 'Utilisateur qui consulte les articles et peut les liker et recevoir des notification.',
        ]);

        Profile::create([
            'user_id' => $abir->id,
            'phone' => '0600000002',
            'bio' => 'Utilisateur qui consulte les articles et peut les liker et recevoir des notification.',
        ]);

        Profile::create([
            'user_id' => $journaliste1->id,
            'phone' => '0600000003',
            'bio' => 'Journaliste1 pour proposer des articles.',
        ]);

        Profile::create([
            'user_id' => $journaliste2->id,
            'phone' => '0600000004',
            'bio' => 'Journaliste2 pour proposer des articles.',
        ]);

        Profile::create([
            'user_id' => $moderateur->id,
            'phone' => '0600000005',
            'bio' => 'Modérateur chargé de vérifier les articles et de donner un score.',
        ]);

        // Cet article est déjà validé pour tester directement l'affichage public.
        $articleValide1 = Article::create([
            'journalist_id' => $journaliste1->id,
            'title' => 'Cybersécurité : les bons réflexes face aux fausses informations',
            'content' => 'Cet article explique comment vérifier une information avant de la partager en ligne.',
            'status' => 'validated',
            'reliability_score' => 90,
            'validated_by' => $moderateur->id,
        ]);

        // Deuxième article validé pour mieux tester le tri et l'affichage public.
        $articleValide2 = Article::create([
            'journalist_id' => $journaliste2->id,
            'title' => 'Réseaux sociaux : comment reconnaître une source fiable',
            'content' => 'Cet article présente des critères simples pour évaluer la fiabilité d’une source.',
            'status' => 'validated',
            'reliability_score' => 75,
            'validated_by' => $moderateur->id,
        ]);

        // Cet article reste en attente pour montrer le travail du modérateur.
        $articleEnAttente = Article::create([
            'journalist_id' => $journaliste1->id,
            'title' => 'Article en attente : nouvelle méthode de vérification',
            'content' => 'Cet article doit encore être contrôlé par un modérateur avant publication.',
            'status' => 'pending',
            'reliability_score' => null,
        ]);

        // Cet article est supprimé pour tester le fait qu'il ne s'affiche plus publiquement.
        $articleSupprime = Article::create([
            'journalist_id' => $journaliste2->id,
            'title' => 'Article supprimé après vérification',
            'content' => 'Cet article a été retiré après analyse par le modérateur.',
            'status' => 'deleted',
            'reliability_score' => 25,
            'validated_by' => $moderateur->id,
            'deleted_by' => $moderateur->id,
        ]);

        // Cet article est restauré pour montrer qu'un article supprimé peut revenir en ligne.
        $articleRestaure = Article::create([
            'journalist_id' => $journaliste1->id,
            'title' => 'Article restauré après correction',
            'content' => 'Cet article avait été supprimé, puis restauré après correction de son contenu.',
            'status' => 'restored',
            'reliability_score' => 80,
            'validated_by' => $moderateur->id,
            'deleted_by' => $moderateur->id,
            'restored_by' => $moderateur->id,
        ]);

        // Je crée plusieurs likes pour tester le compteur de likes et la relation Many-to-Many.
        Like::create([
            'user_id' => $waris->id,
            'article_id' => $articleValide1->id,
        ]);

        Like::create([
            'user_id' => $waris->id,
            'article_id' => $articleRestaure->id,
        ]);

        Like::create([
            'user_id' => $abir->id,
            'article_id' => $articleValide1->id,
        ]);

        Like::create([
            'user_id' => $abir->id,
            'article_id' => $articleValide2->id,
        ]);

        // Ce like sert à montrer qu'un utilisateur peut avoir aimé un article qui a ensuite été supprimé.
        Like::create([
            'user_id' => $abir->id,
            'article_id' => $articleSupprime->id,
        ]);

        // Notifications pour les journalistes après les actions du modérateur.
        Notification::create([
            'user_id' => $journaliste1->id,
            'article_id' => $articleValide1->id,
            'message' => "Votre article '{$articleValide1->title}' a été validé par le modérateur.",
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $journaliste2->id,
            'article_id' => $articleValide2->id,
            'message' => "Votre article '{$articleValide2->title}' a été validé par le modérateur.",
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $journaliste2->id,
            'article_id' => $articleSupprime->id,
            'message' => "Votre article '{$articleSupprime->title}' a été supprimé par le modérateur.",
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $journaliste1->id,
            'article_id' => $articleRestaure->id,
            'message' => "Votre article '{$articleRestaure->title}' a été restauré par le modérateur.",
            'is_read' => false,
        ]);

        // Notifications pour les utilisateurs qui avaient liké des articles.
        Notification::create([
            'user_id' => $abir->id,
            'article_id' => $articleSupprime->id,
            'message' => "Un article que vous aviez aimé a été supprimé : '{$articleSupprime->title}'.",
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $waris->id,
            'article_id' => $articleRestaure->id,
            'message' => "Un article que vous aviez aimé est de nouveau disponible : '{$articleRestaure->title}'.",
            'is_read' => false,
        ]);

        // Notification pour le modérateur afin de montrer qu'il peut aussi recevoir des messages.
        Notification::create([
            'user_id' => $moderateur->id,
            'article_id' => $articleEnAttente->id,
            'message' => "Un nouvel article est en attente de vérification : '{$articleEnAttente->title}'.",
            'is_read' => false,
        ]);

        // Cette notification est déjà lue pour montrer la différence avec les notifications visibles.
        Notification::create([
            'user_id' => $waris->id,
            'article_id' => $articleValide2->id,
            'message' => "Ancienne notification déjà lue pour tester le champ is_read.",
            'is_read' => true,
        ]);
    }
}