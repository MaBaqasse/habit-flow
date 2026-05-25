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
        Schema::create('notification__settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Rappels Quotidiens
            $table->boolean('email_reminder_enabled')->default(true);
            $table->time('reminder_time')->default('08:00:00');
            
            // Résumé Hebdomadaire
            $table->boolean('weekly_summary_enabled')->default(true);
            $table->unsignedTinyInteger('weekly_summary_day')->default(0); // 0=Dimanche
            
            // Alertes de Motivation (Streak)
            $table->boolean('streak_alert_enabled')->default(true);
            
            // Format de l'email (Optionnel mais recommandé dans ta tâche)
            $table->string('email_digest_format')->default('summary'); // summary, detailed, minimal
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification__settings');
    }
};
