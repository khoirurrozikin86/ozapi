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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id(); // Primary key auto increment
            $table->string('no_tagihan')->unique();
            $table->char('id_bulan', 2);
            $table->year('tahun');
            $table->string('id_pelanggan');
            $table->decimal('jumlah_tagihan', 15, 2);
            $table->enum('status', ['belum', 'lunas'])->default('belum');
            $table->date('tgl_bayar')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('remark1')->nullable();
            $table->string('remark2')->nullable();
            $table->string('remark3')->nullable();
            $table->timestamps();

            // Foreign keys (optional)
            $table->foreign('id_bulan')->references('id_bulan')->on('bulans');
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggans')->onDelete('cascade');;
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
