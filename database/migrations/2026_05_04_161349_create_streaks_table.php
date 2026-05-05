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
        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained('habits')->onDelete('cascade');
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->date('last_completed_date')->nullable();
            $table->timestamps();

            $table->unique('habit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streaks');
    }
};
