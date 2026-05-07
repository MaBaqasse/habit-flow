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
        Schema::create('calendar_syncs', function (Blueprint $table) {
            $table->id();
            // FK vers l'utilisateur
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // FK vers l'habitude
            $table->foreignId('habit_id')->constrained()->onDelete('cascade');
            // ID unique fourni par Google Calendar
            $table->string('google_event_id')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_syncs');
    }
};
