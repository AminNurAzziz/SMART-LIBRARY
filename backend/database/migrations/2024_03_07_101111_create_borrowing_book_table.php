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
        Schema::create('borrowing_book', function (Blueprint $table) {
            $table->id();
            $table->string('loan_detail_id', 30)->unique();
            $table->string('code_borrow');
            $table->string('code_book');
            // $table->timestamps();

            // Menambahkan kunci asing untuk kolom kode_pinjam
            $table->foreign('code_borrow')->references('code_borrow')->on('borrowing')->onDelete('cascade');

            // Menambahkan kunci asing untuk kolom kode_buku
            $table->foreign('code_book')->references('code_book')->on('book');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowing_book');
    }
};
