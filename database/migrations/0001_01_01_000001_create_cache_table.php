<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->string('key', 191)->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `cache` COMMENT = 'Cache applicatif Laravel'");
        }

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->string('key', 191)->primary();
            $table->string('owner', 191);
            $table->bigInteger('expiration')->index();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `cache_locks` COMMENT = 'Verrous du cache Laravel'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
