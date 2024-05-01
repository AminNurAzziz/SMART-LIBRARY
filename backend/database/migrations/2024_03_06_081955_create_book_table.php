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
        Schema::create('book', function (Blueprint $table) {
            $table->id();
            $table->string('code_book')->unique();
            $table->string('isbn')->nullable();
            $table->string('title_book');
            $table->string('publisher');
            $table->string('code_category');
            $table->string('code_author');
            $table->string('code_rack');
            $table->integer('stok')->default(0);
            $table->integer('loan_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book');
    }
};
