<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cette migration sert à créer la table likes.
// Elle permet de faire le lien entre un utilisateur et un article qu'il a aimé.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();

            // Ici je lie le like à l'utilisateur qui a aimé l'article.
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Ici je lie le like à l'article qui a été aimé.
            $table->foreignId('article_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // Cette ligne empêche un même utilisateur de liker plusieurs fois le même article.
            // Elle rend donc le couple user_id + article_id unique.
            $table->unique(['user_id', 'article_id']);
        });
    }

    // Cette fonction permet de supprimer la table si on annule la migration.
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
//plusieurs utilisateurs peuvent liker plusieurs articles (Many To Many)