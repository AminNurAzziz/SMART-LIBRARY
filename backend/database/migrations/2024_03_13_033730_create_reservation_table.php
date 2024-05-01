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
        Schema::create('reservation', function (Blueprint $table) {
            $table->id();
            $table->string('code_reservation')->unique();
            $table->string('nim');
            $table->foreign('nim')->references('nim')->on('students');
            $table->date('reservation_date')->timestamp();
            $table->date('date_taken')->nullable();
            $table->enum('status', ['waiting', 'waiting for confirmation', 'accepted', 'failed'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation');
    }
};
