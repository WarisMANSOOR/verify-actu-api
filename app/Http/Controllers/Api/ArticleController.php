<?php

namespace App\Http\Controllers\Api;

// J'importe les classes utiles pour gérer les articles et récupérer les données envoyées dans les requêtes.
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // Cette fonction permet d'afficher les articles visibles publiquement.
    // Je récupère seulement les articles validés ou restaurés, car les articles en attente
    // ou supprimés ne doivent pas être visibles par les utilisateurs.
    public function index(Request $request)
    {
        $query = Article::with('journalist:id,name,email,role')
            ->withCount('likes')
            ->whereIn('status', ['validated', 'restored']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('journalist_id')) {
            $query->where('journalist_id', $request->journalist_id);
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'oldest' => $query->oldest(),
                'most_liked' => $query->orderByDesc('likes_count'),
                'most_reliable' => $query->orderByDesc('reliability_score'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        return response()->json([
            'message' => 'Liste des articles disponibles.',
            'articles' => $query->get(),
        ]);
    }

    // Cette fonction permet d'afficher un seul article.
    // Comme pour la liste, je vérifie que l'article est bien disponible publiquement.
    public function show($id)
    {
        $article = Article::with('journalist:id,name,email,role')
            ->withCount('likes')
            ->whereIn('status', ['validated', 'restored'])
            ->find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article introuvable ou non disponible.',
            ], 404);
        }

        return response()->json([
            'message' => 'Article récupéré avec succès.',
            'article' => $article,
        ]);
    }

    // Cette fonction permet à un journaliste de proposer un article.
    // L'article est créé avec le statut "en attendre"(pending), donc il devra être vérifié
    // par un modérateur avant d'être visible publiquement.
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'journaliste') {
            return response()->json([
                'message' => 'Seul un journaliste peut proposer un article.',
            ], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $article = Article::create([
            'journalist_id' => $user->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => 'pending',
            'reliability_score' => null,
        ]);

        return response()->json([
            'message' => 'Article proposé avec succès. Il est en attente de validation.',
            'article' => $article,
        ], 201);
    }
}