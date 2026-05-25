<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification_Setting extends Model
{
    protected $fillable = [
        'user_id',
        'email_reminder_enabled',
        'reminder_time',
        'weekly_summary_enabled',
        'weekly_summary_day',
        'weekly_summary_time',
        'streak_alert_enabled',
        'email_digest_format',
    ];

    /**
     * Définition des casts pour typer automatiquement les colonnes à la récupération
     */
    protected $casts = [
        'email_reminder_enabled' => 'boolean',
        'weekly_summary_enabled' => 'boolean',
        'streak_alert_enabled' => 'boolean',
        'weekly_summary_day' => 'integer',
        'reminder_time' => 'datetime:H:i',
        'weekly_summary_time' => 'datetime:H:i',
    ];

    /**
     * Relation inverse : Les paramètres appartiennent à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
