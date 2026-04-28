<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cette migration sert à créer la table notifications.
// Elle permet d'envoyer des messages aux utilisateurs, journalistes ou modérateurs.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Ici je lie chaque notification à un utilisateur.
            // Un utilisateur peut recevoir plusieurs notifications (One to Many).
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Ici je peux lier une notification à un article.
            // Le champ est nullable car certaines notifications peuvent être générales.
            $table->foreignId('article_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('message');

            // Ce champ permet de savoir si la notification est encore visible ou pas.
            // false = elle est encore affichée, true = elle est considérée comme supprimée/lue.
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    // Cette fonction permet de supprimer la table si on annule la migration.
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};