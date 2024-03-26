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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam')->unique();
            $table->string('nim');
            $table->foreign('nim')->references('nim')->on('students');
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam', 'dikembalikan'])->default('tersedia');
            $table->decimal('denda', 8, 2)->default(0.00);
            $table->integer('terlambat')->default(0);
            $table->text('informasi_tambahan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
