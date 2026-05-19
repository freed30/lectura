<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique du livre.
            $table->id()->comment("Cle primaire du livre");

            // Relation : chaque livre appartient a un auteur.
            $table->foreignId('author_id')
                ->comment("Auteur lie a ce livre")
                ->constrained('authors')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Titre visible dans la bibliotheque.
            $table->string('title')->comment("Titre du livre");

            // Version simplifiee du titre pour les URL.
            $table->string('slug')->unique()->comment("Slug unique du livre");

            // Resume ou description du contenu.
            $table->longText('description')->nullable()->comment("Description du livre");

            // Code ISBN si disponible.
            $table->string('isbn', 20)->nullable()->unique()->comment("ISBN du livre");

            // Langue principale du livre.
            $table->string('language', 10)->default('fr')->comment("Langue principale du livre");

            // Image de couverture.
            $table->string('cover_image')->nullable()->comment("Chemin de l'image de couverture");

            // Chemin du fichier PDF ou EPUB.
            $table->string('fichier_path')->comment("Chemin du fichier PDF ou EPUB");

            // Format du fichier associe au livre.
            $table->enum('file_format', ['pdf', 'epub'])->comment("Format du fichier du livre");

            // Nombre total de pages si connu.
            $table->unsignedInteger('page_count')->nullable()->comment("Nombre total de pages du livre");

            // Date de publication du livre.
            $table->date('published_at')->nullable()->comment("Date de publication du livre");

            // Prix du livre si la plateforme vend le contenu.
            $table->decimal('price', 10, 2)->default(0)->comment("Prix du livre");

            // Moyenne des notes pour affichage rapide.
            $table->decimal('average_rating', 3, 2)->default(0)->comment("Moyenne des notes du livre");

            // Etat de publication dans le catalogue.
            $table->boolean('is_published')->default(true)->comment("Indique si le livre est visible");

            // Date de creation du livre.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation du livre");

            // Date de mise a jour du livre.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour du livre");
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `books` COMMENT = 'Livres disponibles pour lecture'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
