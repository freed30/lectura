<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique du favori.
            $table->id()->comment("Cle primaire du favori");

            // Relation : chaque favori appartient a un utilisateur.
            $table->foreignId('user_id')
                ->comment("Utilisateur proprietaire du favori")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relation : chaque favori pointe vers un livre.
            $table->foreignId('book_id')
                ->comment("Livre ajoute en favori")
                ->constrained('books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Date d'ajout dans les favoris.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date d'ajout en favori");

            // Date de mise a jour de l'entree favorite.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour du favori");

            // Un livre ne peut etre ajoute qu'une seule fois en favori par utilisateur.
            $table->unique(['user_id', 'book_id'], 'favorites_user_book_unique');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `favorites` COMMENT = 'Livres favoris des utilisateurs'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
