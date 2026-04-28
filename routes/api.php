<?php

use App\Models\Article;
use App\Models\Notification;
use App\Models\Like;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ModeratorArticleController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NotificationController;

// Ici je mets les routes publiques, donc celles qu'on peut utiliser sans être connecté.
// On peut créer un compte, se connecter, et consulter les articles déjà validés.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

// Ici je regroupe toutes les routes protégées.
// Pour les utiliser, il faut envoyer un token avec la requête.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function () {
        return auth()->user();
    });

    // Cette route permet à un journaliste connecté de proposer un article.
    Route::post('/articles', [ArticleController::class, 'store']);

    // Ces routes sont utilisées par le modérateur pour gérer les articles.
    // Il peut voir tous les articles, mettre un score, valider, supprimer ou restaurér.
    Route::get('/moderator/articles', [ModeratorArticleController::class, 'index']);
    Route::patch('/moderator/articles/{id}/score', [ModeratorArticleController::class, 'updateScore']);
    Route::patch('/moderator/articles/{id}/validate', [ModeratorArticleController::class, 'validateArticle']);
    Route::delete('/moderator/articles/{id}', [ModeratorArticleController::class, 'deleteArticle']);
    Route::patch('/moderator/articles/{id}/restore', [ModeratorArticleController::class, 'restoreArticle']);

    // Ces routes servent à liker ou retirer le like d'un article.
    // Elles sont réservée aux utilisateurs connectés.
    Route::post('/articles/{id}/like', [LikeController::class, 'like']);
    Route::delete('/articles/{id}/like', [LikeController::class, 'unlike']);

    // Ces routes servent à consulter et supprimer les notifications.
    // Quand on supprime, on voit plus la notification, donc ça veut dire que is_read est passé à true.
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);

    
});
// Ces routes servent seulement à faciliter la démonstration du projet.
// Elles permettent de voir les données de test directement dans le navigateur.
Route::get('/demo/articles-all', function () {
    return response()->json([
        'message' => 'Liste de tous les articles, y compris pending et deleted.',
        'articles' => Article::with('journalist:id,name,email,role')
            ->withCount('likes')
            ->latest()
            ->get(),
    ]);
});

Route::get('/demo/likes', function () {
    return response()->json([
        'message' => 'Liste des likes de démonstration.',
        'likes' => Like::with([
            'user:id,name,email,role',
            'article:id,title,status'
        ])->get(),
    ]);
});

Route::get('/demo/notifications', function () {
    return response()->json([
        'message' => 'Liste des notifications de démonstration.',
        'notifications' => Notification::with([
            'user:id,name,email,role',
            'article:id,title,status'
        ])->latest()->get(),
    ]);
});