<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique de l'avis.
            $table->id()->comment("Cle primaire de l'avis");

            // Relation : chaque avis appartient a un utilisateur.
            $table->foreignId('user_id')
                ->comment("Utilisateur qui a ecrit l'avis")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Relation : chaque avis concerne un livre.
            $table->foreignId('book_id')
                ->comment("Livre concerne par l'avis")
                ->constrained('books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Note donnee par le lecteur.
            $table->unsignedTinyInteger('rating')->comment("Note du livre sur 5");

            // Texte de l'avis laisse par l'utilisateur.
            $table->text('review_text')->nullable()->comment("Contenu de l'avis");

            // Permet de masquer un avis si besoin.
            $table->boolean('is_visible')->default(true)->comment("Indique si l'avis est visible");

            // Date de creation de l'avis.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation de l'avis");

            // Date de mise a jour de l'avis.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour de l'avis");

            // Un utilisateur ne peut laisser qu'un seul avis par livre.
            $table->unique(['user_id', 'book_id'], 'reviews_user_book_unique');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `reviews` COMMENT = 'Avis et notes laisses par les lecteurs'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
