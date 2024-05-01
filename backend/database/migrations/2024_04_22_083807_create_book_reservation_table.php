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
        Schema::create('book_reservation', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_details_id', 30)->unique();
            $table->string('code_reservation');
            $table->string('code_book');
            $table->timestamps();

            // Add foreign key for kode_reservasi column
            $table->foreign('code_reservation')->references('code_reservation')->on('reservation');

            // Add foreign key for kode_buku column
            $table->foreign('code_book')->references('code_book')->on('book');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reservation');
    }
};
