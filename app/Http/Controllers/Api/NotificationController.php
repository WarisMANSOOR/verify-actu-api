<?php

namespace App\Http\Controllers\Api;

// J'importe les classes nécessaires pour gérer les notifications
// et récupérer l'utilisateur connecté dans la requête.
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Cette fonction permet d'afficher les notifications de l'utilisateur connecté.
    // Je n'affiche que les notifications qui sont pzs lues, car les notifications supprimées
    // sont juste marquées comme lues avec is_read = true.
    public function index(Request $request)
    {
        $notifications = Notification::with('article:id,title,status')
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Liste des notifications.',
            'notifications' => $notifications,
        ]);
    }

    // Cette fonction permet de supprimer une seule notification.
    // En réalité je ne la supprime pas vraiment de la base, je la marque comme lue
    // pour garder une trace tout en la cachant de l'affichage.
    public function destroy(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification introuvable.',
            ], 404);
        }

        $notification->update([
            'is_read' => true,
        ]);

        return response()->json([
            'message' => 'Notification supprimée avec succès.',
        ]);
    }

    // Cette fonction permet de supprimer toutes les notifications visibles.
    // Comme pour une seule notification, je passe simplement is_read à true pour toutes.
    public function destroyAll(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return response()->json([
            'message' => 'Toutes les notifications ont été supprimées avec succès.',
        ]);
    }
}