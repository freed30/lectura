<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique de l'entree wishlist.
            $table->id()->comment("Cle primaire de la wishlist");

            // Relation : chaque entree wishlist appartient a un utilisateur.
            $table->foreignId('user_id')
                ->comment("Utilisateur proprietaire de la wishlist")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relation : chaque entree wishlist concerne un livre.
            $table->foreignId('book_id')
                ->comment("Livre ajoute a la wishlist")
                ->constrained('books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Note personnelle optionnelle.
            $table->string('note')->nullable()->comment("Petite note personnelle sur le livre");

            // Priorite de lecture choisie par l'utilisateur.
            $table->unsignedTinyInteger('priority')->default(1)->comment("Priorite de lecture du livre");

            // Date d'ajout dans la wishlist.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date d'ajout dans la wishlist");

            // Date de mise a jour de l'entree wishlist.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour de la wishlist");

            // Un livre ne peut apparaitre qu'une fois par utilisateur dans la wishlist.
            $table->unique(['user_id', 'book_id'], 'wishlists_user_book_unique');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `wishlists` COMMENT = 'Livres que l utilisateur veut lire plus tard'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
