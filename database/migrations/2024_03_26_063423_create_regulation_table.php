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
        Schema::create('regulation', function (Blueprint $table) {
            $table->id();
            $table->integer('max_loan_days')->default(7);
            $table->integer('max_loan_books')->default(2);
            $table->integer('max_reserve_books')->default(2);
            $table->integer('max_reserve_days')->default(1);
            $table->decimal('fine_per_day', 8, 2)->default(500.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulation');
    }
};
