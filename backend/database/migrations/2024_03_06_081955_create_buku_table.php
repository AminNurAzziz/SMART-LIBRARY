<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBukuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku')->unique();
            $table->string('isbn')->nullable();
            $table->string('judul_buku');
            $table->string('penerbit');
            $table->string('kode_kategori');
            $table->string('kode_penulis');
            $table->string('kode_rak');
            $table->integer('stok')->default(0);
            $table->integer('jumlah_peminjam')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
}
