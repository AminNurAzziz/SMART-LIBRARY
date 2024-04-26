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
        Schema::table('buku_reservasi', function (Blueprint $table) {
            $table->date('tanggal_reservasi')->nullable()->after('kode_buku');
            $table->date('tanggal_ambil')->nullable()->after('tanggal_reservasi');
            $table->enum('status', ['menunggu', 'menunggu konfirmasi', 'diterima', 'gagal'])->default('menunggu')->after('tanggal_ambil');
        });

        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn(['tanggal_reservasi', 'tanggal_ambil', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->date('tanggal_reservasi')->nullable();
            $table->date('tanggal_ambil')->nullable();
            $table->enum('status', ['menunggu', 'menunggu konfirmasi', 'diterima', 'gagal'])->default('menunggu');
        });

        Schema::table('buku_reservasi', function (Blueprint $table) {
            $table->dropColumn(['tanggal_reservasi', 'tanggal_ambil', 'status']);
        });
    }
};
