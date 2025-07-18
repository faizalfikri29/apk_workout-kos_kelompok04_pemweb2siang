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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('workout_id');
            $table->integer('duration_seconds'); // <== Pastikan baris ini ada
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
