<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique de la progression.
            $table->id()->comment("Cle primaire de la progression");

            // Relation : chaque progression appartient a un utilisateur.
            $table->foreignId('user_id')
                ->comment("Utilisateur qui lit le livre")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relation : chaque progression concerne un livre.
            $table->foreignId('book_id')
                ->comment("Livre suivi dans la progression")
                ->constrained('books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Derniere page lue pour un PDF ou une lecture paginee.
            $table->unsignedInteger('current_page')->default(0)->comment("Derniere page lue");

            // Derniere position lue pour un EPUB.
            $table->string('current_location')->nullable()->comment("Derniere position lue dans un EPUB");

            // Nombre total de pages retenu pour le calcul.
            $table->unsignedInteger('total_pages')->default(0)->comment("Nombre total de pages du livre");

            // Pourcentage de progression entre 0 et 100.
            $table->decimal('progress_percent', 5, 2)->default(0)->comment("Pourcentage de progression de lecture");

            // Indique si le lecteur a termine le livre.
            $table->boolean('is_finished')->default(false)->comment("Indique si la lecture est terminee");

            // Dernier moment ou le livre a ete ouvert.
            $table->timestamp('last_read_at')->nullable()->comment("Date de la derniere lecture");

            // Date de fin complete de la lecture.
            $table->timestamp('completed_at')->nullable()->comment("Date de fin de lecture");

            // Date de creation de la progression.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation de la progression");

            // Date de mise a jour de la progression.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour de la progression");

            // Un utilisateur ne peut avoir qu'une seule progression par livre.
            $table->unique(['user_id', 'book_id'], 'reading_progress_user_book_unique');

            // Index utile pour reprendre rapidement les dernieres lectures.
            $table->index(['user_id', 'last_read_at'], 'reading_progress_user_last_read_index');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `reading_progress` COMMENT = 'Suivi de la progression de lecture des utilisateurs'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_progress');
    }
};
