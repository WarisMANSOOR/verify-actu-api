<?php

namespace App\Http\Controllers\Api;

// J'importe les classes nécessaires pour gérer les articles,
// les likes et les requêtes envoyées à l'API.
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    // Cette fonction permet à un utilisateur de liker un article.
    // Je vérifie d'abord que la personne connectée est bien un utilisateur,
    // puis je vérifie aussi que l'article est disponible publiquement.
    public function like(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'utilisateur') {
            return response()->json([
                'message' => 'Seul un utilisateur peut liker un article.',
            ], 403);
        }

        $article = Article::whereIn('status', ['validated', 'restored'])->find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article introuvable ou non disponible.',
            ], 404);
        }

        $alreadyLiked = Like::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'message' => 'Vous avez déjà liké cet article.',
            ], 409);
        }

        $like = Like::create([
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);

        return response()->json([
            'message' => 'Article liké avec succès.',
            'like' => $like,
        ], 201);
    }

    // Cette fonction permet à un utilisateur de retirer son like.
    // Je vérifie que le like existe vraiment avant de le supprimer,
    // pour éviter de retirer un like qui n'existe pas.
    public function unlike(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'utilisateur') {
            return response()->json([
                'message' => 'Seul un utilisateur peut retirer son like.',
            ], 403);
        }

        $article = Article::whereIn('status', ['validated', 'restored'])->find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article introuvable ou non disponible.',
            ], 404);
        }

        $like = Like::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if (!$like) {
            return response()->json([
                'message' => 'Vous n’avez pas encore liké cet article.',
            ], 404);
        }

        $like->delete();

        return response()->json([
            'message' => 'Like retiré avec succès.',
        ]);
    }
}