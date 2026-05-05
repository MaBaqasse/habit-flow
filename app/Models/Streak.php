<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Streak extends Model
{
    protected $fillable = ['habit_id', 'current_streak', 'best_streak', 'last_completed_date'];

    protected $casts = [
        'last_completed_date' => 'date',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
