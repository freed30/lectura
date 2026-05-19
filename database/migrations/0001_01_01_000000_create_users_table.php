<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique de l'utilisateur.
            $table->id()->comment("Cle primaire de l'utilisateur");

            // Nom affiche dans l'application.
            $table->string('name')->comment("Nom complet ou pseudo de l'utilisateur");

            // Adresse email utilisee pour la connexion.
            $table->string('email')->unique()->comment("Adresse email unique de l'utilisateur");

            // Date de verification de l'email.
            $table->timestamp('email_verified_at')->nullable()->comment("Date de verification de l'email");

            // Mot de passe stocke sous forme securisee.
            $table->string('password')->comment("Mot de passe hashe de l'utilisateur");

            // Role simple pour distinguer lecteur et administrateur.
            $table->string('role')->default('reader')->comment("Role de l'utilisateur dans l'application");

            // Image de profil optionnelle.
            $table->string('avatar')->nullable()->comment("Chemin de l'avatar de l'utilisateur");

            // Jeton de session "se souvenir de moi".
            $table->rememberToken()->comment("Jeton de memorisation de session");

            // Date de creation de l'utilisateur.
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation du compte");

            // Date de derniere mise a jour de l'utilisateur.
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour du compte");
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` COMMENT = 'Utilisateurs de la plateforme de lecture'");
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Email du compte qui demande la reinitialisation.
            $table->string('email')->primary()->comment("Email utilise pour reinitialiser le mot de passe");

            // Jeton temporaire de reinitialisation.
            $table->string('token')->comment("Jeton de reinitialisation du mot de passe");

            // Date de creation du jeton.
            $table->timestamp('created_at')->nullable()->comment("Date de creation du jeton");
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `password_reset_tokens` COMMENT = 'Jetons de reinitialisation des mots de passe'");
        }

        Schema::create('sessions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Identifiant unique de la session.
            $table->string('id')->primary()->comment("Cle primaire de la session");

            // Relation : une session peut appartenir a un utilisateur connecte.
            $table->foreignId('user_id')
                ->nullable()
                ->comment("Utilisateur lie a la session")
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Adresse IP de la session.
            $table->string('ip_address', 45)->nullable()->comment("Adresse IP de la session");

            // Navigateur ou client utilise.
            $table->text('user_agent')->nullable()->comment("Informations du navigateur utilise");

            // Donnees serializees de la session.
            $table->longText('payload')->comment("Contenu technique de la session");

            // Horodatage de la derniere activite.
            $table->integer('last_activity')->index()->comment("Derniere activite en timestamp Unix");
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `sessions` COMMENT = 'Sessions des utilisateurs connectes'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
