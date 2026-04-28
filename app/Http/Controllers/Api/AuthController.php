<?php

namespace App\Http\Controllers\Api;

// J'importe ici les classes dont j'ai besoin pour gérer les utilisateurs,
// les profils, les requêtes, le hash du mot de passe et les erreurs de connexion.
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Cette fonction sert à créer un nouveau compte dans l'application.
    // Elle vérifie les données envoyées, crée l'utilisateur, crée aussi son profil,
    // puis renvoyer un token API pour utiliser les routes protégéés.
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:utilisateur,journaliste,moderateur',
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        Profile::create([
            'user_id' => $user->id,
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Cette fonction sert à connecter un utilisateur.
    // Elle vérifie si l'email existe et si le mot de passe sont les bons.
    // Si tout est bon, elle renvoie un token qui servira pour les requêtes suivantes.
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Cette fonction sert à déconnecter l'utilisateur.
    // En réalité, on supprime juste le token utilisé pour la session API actuelle.
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }
}