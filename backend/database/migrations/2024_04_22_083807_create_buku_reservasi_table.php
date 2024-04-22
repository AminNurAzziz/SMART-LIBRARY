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
        Schema::create('buku_reservasi', function (Blueprint $table) {
            $table->id();
            $table->string('id_detail_reservasi', 30)->unique();
            $table->string('kode_reservasi');
            $table->string('kode_buku');
            $table->timestamps();

            // Add foreign key for kode_reservasi column
            $table->foreign('kode_reservasi')->references('kode_reservasi')->on('reservasi');

            // Add foreign key for kode_buku column
            $table->foreign('kode_buku')->references('kode_buku')->on('bukus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_reservasi');
    }
};
