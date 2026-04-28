<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cette migration sert à créer la table articles.
// Elle contient les articles proposés par les journalistes.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            // Ici je lie chaque article à un journaliste.
            // Cela montre qu'un journaliste peut avoir plusieurs articles(One to Many).
            //et que plusieurs articles peuvent appartenir à un seul  journalisyte (Many to One)
            $table->foreignId('journalist_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('content');

            // Ici je garde le statut de l'article.
            // Il permet de savoir si l'article est en attente(pending), validé, supprimé ou restauré.
            $table->enum('status', [
                'pending',
                'validated',
                'deleted',
                'restored'
            ])->default('pending');

            // Ce champ contient le score de fiabilité donné par le modérateur.
            // Il peut être null au début, car l'article n'est pas encore vérifié.
            $table->unsignedTinyInteger('reliability_score')->nullable();

            // Ici je garde l'id du modérateur qui a validé l'article.
            $table->foreignId('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Et ici je garde l'id du modérateur qui a supprimé l'article.
            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Et ici je garde l'id du modérateur qui a restauré l'article.
            $table->foreignId('restored_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    // Cette fonction permet de supprimer la table si on annule la migration.
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};