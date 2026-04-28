<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cette migration sert à créer la table users.
// Elle contient les comptes des utilisateurs, journalistes et modérateurs.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Ici je stocke les informations principales du compte.
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Ce champ permet de savoir le rôle du compte dans l'application.
            // Il sert ensuite à autoriser ou bloquer certaines actions dans l'API.
            $table->enum('role', ['utilisateur', 'journaliste', 'moderateur'])
                ->default('utilisateur');

            $table->rememberToken();
            $table->timestamps();
        });

        // Cette table est générée par Laravel pour gérer la réinitialisation des mots de passe.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Cette table sert à gérer les sessions Laravel.
        // Même si nous utilisons surtout les tokens API, elle fait partie de la structure Laravel.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    // Cette fonction permet de supprimer les tables si on annule la migration.
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};