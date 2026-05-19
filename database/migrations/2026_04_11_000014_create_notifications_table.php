<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->uuid('id')->primary()->comment("Cle primaire de la notification");

            // Type de notification stocke en base.
            $table->string('type', 191)->comment("Type de la notification");

            // Utilisateur proprietaire de la notification.
            $table->string('notifiable_type', 191)->comment("Type du modele notifie");

            // Utilisateur proprietaire de la notification.
            $table->unsignedBigInteger('notifiable_id')->comment("Identifiant du modele notifie");

            // Contenu de la notification pour l interface.
            $table->json('data')->comment("Donnees de la notification");

            // Date de lecture de la notification.
            $table->timestamp('read_at')->nullable()->comment("Date de lecture");

            $table->timestamp('created_at')->nullable()->useCurrent()->comment("Date de creation");
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->comment("Date de mise a jour");

            $table->index(['notifiable_type', 'notifiable_id'], 'notifications_notifiable_index');
            $table->index(['notifiable_id', 'read_at'], 'notifications_user_read_index');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `notifications` COMMENT = 'Notifications utilisateurs stockees en base'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
