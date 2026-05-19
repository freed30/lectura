<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique de la recommandation.
            $table->id()->comment("Cle primaire de la recommandation");

            // Relation : chaque recommandation appartient a un utilisateur.
            $table->foreignId('user_id')
                ->comment("Utilisateur qui recoit la recommandation")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relation : livre recommande a l'utilisateur.
            $table->foreignId('book_id')
                ->comment("Livre recommande")
                ->constrained('books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relation : livre source qui a aide a calculer la recommandation.
            $table->foreignId('based_on_book_id')
                ->nullable()
                ->comment("Livre source utilise pour la recommandation")
                ->constrained('books')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Explication courte de la suggestion.
            $table->string('reason')->nullable()->comment("Raison simple de la recommandation");

            // Score de pertinence calcule.
            $table->decimal('score', 5, 2)->default(0)->comment("Score de pertinence de la recommandation");

            // Indique si l'utilisateur a deja vu la suggestion.
            $table->boolean('is_seen')->default(false)->comment("Indique si la recommandation a ete vue");

            // Date de creation de la recommandation.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation de la recommandation");

            // Date de mise a jour de la recommandation.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour de la recommandation");

            // Evite les doublons de recommandation par utilisateur et par livre.
            $table->unique(['user_id', 'book_id'], 'recommendations_user_book_unique');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `recommendations` COMMENT = 'Suggestions de livres personnalisees'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
