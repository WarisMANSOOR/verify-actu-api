<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cette migration sert à créer la table profiles.
// Elle permet d'ajouter des informations en plus sur un utilisateur.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // Ici je lie le profil à un utilisateur.
            // Le unique permet de dire qu'un utilisateur ne peut avoir qu'un seul profil(One to One).
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('phone')->nullable();
            $table->text('bio')->nullable();

            $table->timestamps();
        });
    }

    // Cette fonction permet de supprimer la table si on annule la migration.
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};