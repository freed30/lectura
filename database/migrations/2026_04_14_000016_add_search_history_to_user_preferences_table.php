<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table): void {
            // historique simple des recherches pour personnaliser les suggestions.
            $table->json('search_history')
                ->nullable()
                ->after('immersive_mode_default')
                ->comment('recherches recentes du lecteur');
        });
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table): void {
            $table->dropColumn('search_history');
        });
    }
};
