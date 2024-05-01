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
        Schema::table('book_reservation', function (Blueprint $table) {
            $table->date('reservation_date')->nullable()->after('code_book');
            $table->date('date_taken')->nullable()->after('reservation_date');
            $table->enum('status', ['waiting', 'waiting for confirmation', 'accepted', 'failed'])->default('waiting')->after('date_taken');
        });

        Schema::table('reservation', function (Blueprint $table) {
            $table->dropColumn(['reservation_date', 'date_taken', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->date('reservation_date')->nullable();
            $table->date('date_taken')->nullable();
            $table->enum('status', ['waiting', 'waiting for confirmation', 'confirmed', 'accepted', 'failed'])->default('waiting');
        });

        Schema::table('book_reservation', function (Blueprint $table) {
            $table->dropColumn(['reservation_date', 'date_taken', 'status']);
        });
    }
};
