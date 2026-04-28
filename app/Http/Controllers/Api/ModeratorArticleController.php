<?php

namespace App\Http\Controllers\Api;

// J'importe les classes nécessaires pour gérer les articles,
// les notifications et les requêtes envoyées à l'API.
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Notification;
use Illuminate\Http\Request;

class ModeratorArticleController extends Controller
{
    // Cette petite fonction sert à vérifier que l'utilisateur connecté est bien un modérateur.
    // Je l'utilise dans plusieurs méthodes pour éviter de répéter le même test partout.
    private function checkModerator($user)
    {
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non connecté.',
            ], 401);
        }

        if (trim($user->role) !== 'moderateur') {
            return response()->json([
                'message' => 'Action réservée au modérateur.',
                'role_detecte' => $user->role,
            ], 403);
        }

        return null;
    }

    // Cette fonction permet au modérateur de voir tous les articles.
    // Contrairement à la page d'acceuil ou la page de l'utilisateur, ici on affiche aussi les articles en attente ou supprimés.
    public function index(Request $request)
    {
        if ($error = $this->checkModerator($request->user())) {
            return $error;
        }

        $articles = Article::with('journalist:id,name,email,role')
            ->withCount('likes')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Liste des articles pour modération.',
            'articles' => $articles,
        ]);
    }

    // Cette fonction permet au modérateur d'attribuer ou modifier le score de fiabilité.
    // Le score doit être compris entre 0 et 100 pour rester simple et clair.
    public function updateScore(Request $request, $id)
    {
        if ($error = $this->checkModerator($request->user())) {
            return $error;
        }

        $data = $request->validate([
            'reliability_score' => 'required|integer|min:0|max:100',
        ]);

        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article introuvable.',
            ], 404);
        }

        $article->update([
            'reliability_score' => $data['reliability_score'],
        ]);

        return response()->json([
            'message' => 'Score de fiabilité enregistré avec succès.',
            'article' => $article,
        ]);
    }

    // Cette fonction permet au modérateur de valider un article.
    // on bloque la validation si aucun score n'a été donné.
    public function validateArticle(Request $request, $id)
    {
        if ($error = $this->checkModerator($request->user())) {
            return $error;
        }

        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article introuvable.',
            ], 404);
        }

        if ($article->status === 'deleted') {
            return response()->json([
                'message' => 'Impossible de valider un article supprimé.',
            ], 409);
        }

        if ($article->reliability_score === null) {
            return response()->json([
                'message' => 'Veuillez attribuer un score de fiabilité avant validation.',
            ], 409);
        }

        $article->update([
            'status' => 'validated',
            'validated_by' => $request->user()->id,
        ]);

        Notification::create([
            'user_id' => $article->journalist_id,
            'article_id' => $article->id,
            'message' => "Votre article '{$article->title}' a été validé par le modérateur.",
        ]);

        return response()->json([
            'message' => 'Article validé avec succès.',
            'article' => $article,
        ]);
    }

    // Cette fonction permet au modérateur de supprimer un article.
    // L'article n'est pas vraiment supprimé de la base, on change juste son statut en "deleted" et on le cache.
    public function deleteArticle(Request $request, $id)
    {
        if ($error = $this->checkModerator($request->user())) {
            return $error;
        }

        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article introuvable.',
            ], 404);
        }

        if ($article->status === 'deleted') {
            return response()->json([
                'message' => 'Article déjà supprimé.',
            ], 409);
        }

        $article->update([
            'status' => 'deleted',
            'deleted_by' => $request->user()->id,
        ]);

        Notification::create([
            'user_id' => $article->journalist_id,
            'article_id' => $article->id,
            'message' => "Votre article '{$article->title}' a été supprimé par le modérateur.",
        ]);

        foreach ($article->likedByUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'article_id' => $article->id,
                'message' => "Un article que vous aviez aimé a été supprimé : '{$article->title}'.",
            ]);
        }

        return response()->json([
            'message' => 'Article supprimé avec succès.',
            'article' => $article,
        ]);
    }

    // Cette fonction permet au modérateur de restaurer un article supprimé.
    // Quand l'article revient en ligne, le journaliste et les utilisateurs concernés sont notifiés.
    public function restoreArticle(Request $request, $id)
    {
        if ($error = $this->checkModerator($request->user())) {
            return $error;
        }

        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article introuvable.',
            ], 404);
        }

        if ($article->status !== 'deleted') {
            return response()->json([
                'message' => 'Seul un article supprimé peut être restauré.',
            ], 409);
        }

        $article->update([
            'status' => 'restored',
            'restored_by' => $request->user()->id,
        ]);

        Notification::create([
            'user_id' => $article->journalist_id,
            'article_id' => $article->id,
            'message' => "Votre article '{$article->title}' a été restauré par le modérateur.",
        ]);

        foreach ($article->likedByUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'article_id' => $article->id,
                'message' => "Un article que vous aviez aimé est de nouveau disponible : '{$article->title}'.",
            ]);
        }

        return response()->json([
            'message' => 'Article restauré avec succès.',
            'article' => $article,
        ]);
    }
}