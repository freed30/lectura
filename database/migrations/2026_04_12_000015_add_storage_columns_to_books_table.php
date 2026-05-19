<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // disque Laravel utilise pour retrouver le livre apres deploiement.
            $table->string('file_disk', 50)
                ->default('public')
                ->after('fichier_path')
                ->comment('Disque Laravel du fichier du livre');

            // type MIME du fichier pour l affichage direct dans le navigateur.
            $table->string('file_mime_type', 150)
                ->nullable()
                ->after('file_format')
                ->comment('Type MIME du fichier du livre');

            // taille du fichier pour controle et streaming.
            $table->unsignedBigInteger('file_size')
                ->nullable()
                ->after('file_mime_type')
                ->comment('Taille du fichier du livre en octets');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['file_disk', 'file_mime_type', 'file_size']);
        });
    }
};
