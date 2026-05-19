<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_history', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id()->comment("Cle primaire de l historique");

            // Relation : historique relie un utilisateur a un livre.
            $table->foreignId('user_id')
                ->comment("Utilisateur de l historique de lecture")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relation : historique relie un utilisateur a un livre.
            $table->foreignId('book_id')
                ->comment("Livre conserve dans l historique")
                ->constrained('books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamp('started_at')->nullable()->comment("Date de debut de lecture");
            $table->timestamp('last_opened_at')->nullable()->comment("Derniere ouverture du livre");
            $table->decimal('last_progress_percent', 5, 2)->default(0)->comment("Derniere progression connue");
            $table->timestamp('completed_at')->nullable()->comment("Date de fin de lecture");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation");
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour");

            $table->unique(['user_id', 'book_id'], 'reading_history_user_book_unique');
            $table->index(['user_id', 'last_opened_at'], 'reading_history_user_opened_index');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `reading_history` COMMENT = 'Historique de lecture des utilisateurs'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_history');
    }
};
