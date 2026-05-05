<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Habit_Completion extends Model
{
    // On utilise le nom de table spécifié dans votre PRD
    protected $table = 'habit_completions';

    protected $fillable = ['habit_id', 'user_id', 'completed_date', 'note'];

    // Cast de la date pour faciliter les comparaisons dans le StreakCalculator
    protected $casts = [
        'completed_date' => 'date',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
