<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id()->comment("Cle primaire de la preference");

            // Relation : chaque preference appartient a un utilisateur.
            $table->foreignId('user_id')
                ->unique()
                ->comment("Utilisateur proprietaire des preferences")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('theme')->default('dark')->comment("Theme prefere du lecteur");
            $table->string('font_size')->default('medium')->comment("Taille de police preferee");
            $table->string('line_spacing')->default('comfortable')->comment("Interligne prefere");
            $table->boolean('page_flip_enabled')->default(true)->comment("Active ou non l animation de page");
            $table->boolean('immersive_mode_default')->default(false)->comment("Active ou non le mode immersive par defaut");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation");
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour");
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `user_preferences` COMMENT = 'Preferences de lecture par utilisateur'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
