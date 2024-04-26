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
        Schema::table('buku_peminjaman', function (Blueprint $table) {
            $table->date('tgl_pinjam')->nullable();
            $table->date('tgl_kembali')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam', 'dikembalikan'])->default('tersedia');
            $table->decimal('denda', 8, 2)->default(0.00);
            $table->integer('terlambat')->default(0);
            $table->text('informasi_tambahan')->nullable();
            $table->timestamps();
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['tgl_pinjam', 'tgl_kembali', 'status', 'denda', 'terlambat', 'informasi_tambahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam', 'dikembalikan'])->default('tersedia');
            $table->decimal('denda', 8, 2)->default(0.00);
            $table->integer('terlambat')->default(0);
            $table->text('informasi_tambahan')->nullable();
        });

        Schema::table('buku_peminjaman', function (Blueprint $table) {
            $table->dropColumn(['tgl_pinjam', 'tgl_kembali', 'status', 'denda', 'terlambat', 'informasi_tambahan']);
        });
    }
};
