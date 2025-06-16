<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Ubah kolom menjadi tidak nullable
            $table->unsignedBigInteger('id_paket')->nullable(false)->change();
            $table->unsignedBigInteger('id_server')->nullable(false)->change();

            // Tambahkan foreign key constraint
            $table->foreign('id_paket')->references('id')->on('pakets')->cascadeOnDelete();
            $table->foreign('id_server')->references('id')->on('servers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Drop foreign keys terlebih dahulu
            $table->dropForeign(['id_paket']);
            $table->dropForeign(['id_server']);

            // Kembalikan kolom menjadi nullable (jika diperlukan)
            $table->unsignedBigInteger('id_paket')->nullable()->change();
            $table->unsignedBigInteger('id_server')->nullable()->change();
        });
    }
};
