<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique de l'auteur.
            $table->id()->comment("Cle primaire de l'auteur");

            // Nom principal de l'auteur.
            $table->string('name')->comment("Nom complet de l'auteur");

            // Presentation biographique.
            $table->text('biography')->nullable()->comment("Biographie de l'auteur");

            // Date de naissance si elle est connue.
            $table->date('birth_date')->nullable()->comment("Date de naissance de l'auteur");

            // Pays d'origine de l'auteur.
            $table->string('country')->nullable()->comment("Pays d'origine de l'auteur");

            // Photo ou portrait de l'auteur.
            $table->string('photo')->nullable()->comment("Chemin de la photo de l'auteur");

            // Date de creation de l'auteur.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation de l'auteur");

            // Date de mise a jour de l'auteur.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour de l'auteur");
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `authors` COMMENT = 'Auteurs des livres disponibles'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
