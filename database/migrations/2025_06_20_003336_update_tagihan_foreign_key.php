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
        Schema::table('tagihans', function (Blueprint $table) {
            // Drop existing foreign key if it exists
            $table->dropForeign(['id_pelanggan']);

            // Add foreign key with cascading delete
            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('pelanggans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Drop foreign key if rolling back the migration
            $table->dropForeign(['id_pelanggan']);
        });
    }
};
