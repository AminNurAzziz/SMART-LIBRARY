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
        Schema::table('borrowing_book', function (Blueprint $table) {
            $table->date('loan_date')->nullable();
            $table->date('return_date')->nullable();
            $table->enum('status', ['available', 'borrowed', 'returned'])->default('available');
            $table->decimal('fine', 8, 2)->default(0.00);
            $table->integer('late')->default(0);
            $table->text('more_information')->nullable();
            $table->timestamps();
        });

        Schema::table('borrowing', function (Blueprint $table) {
            $table->dropColumn(['loan_date', 'return_date', 'status', 'fine', 'late', 'more_information']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowing', function (Blueprint $table) {
            $table->date('loan_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['available', 'borrowed', 'returned'])->default('available');
            $table->decimal('fine', 8, 2)->default(0.00);
            $table->integer('late')->default(0);
            $table->text('more_information')->nullable();
        });

        Schema::table('borrowing_book', function (Blueprint $table) {
            $table->dropColumn(['loan_date', 'return_date', 'status', 'fine', 'late', 'more_information']);
        });
    }
};
