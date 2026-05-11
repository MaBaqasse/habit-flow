<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarSync extends Model
{
    protected $fillable = [
        'user_id',
        'habit_id',
        'google_event_id',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'habitude
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
