<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buku_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('id_detail_pinjam', 30)->unique();
            $table->string('kode_pinjam');
            $table->string('kode_buku');
            // $table->timestamps();

            // Menambahkan kunci asing untuk kolom kode_pinjam
            $table->foreign('kode_pinjam')->references('kode_pinjam')->on('peminjaman')->onDelete('cascade');

            // Menambahkan kunci asing untuk kolom kode_buku
            $table->foreign('kode_buku')->references('kode_buku')->on('bukus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_peminjaman');
    }
};
