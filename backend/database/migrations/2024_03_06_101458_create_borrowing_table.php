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
        Schema::create('borrowing', function (Blueprint $table) {
            $table->id();
            $table->string('code_borrow')->unique()->cascade();
            $table->string('nim');
            $table->foreign('nim')->references('nim')->on('students');
            $table->date('loan_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['available', 'borrowed', 'returned'])->default('available');
            $table->decimal('fine', 8, 2)->default(0.00);
            $table->integer('late')->default(0);
            $table->text('more_information')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowing');
    }
};
