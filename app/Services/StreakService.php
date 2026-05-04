<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\Habit_Completion;
use App\Models\Streak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StreakService
{
    /**
     * Enregistre une complétion et met à jour le streak associé.
     */
    public function recordCompletion(Habit $habit, int $user_id, $note = null)
    {
        $today = Carbon::today();

        return DB::transaction(function () use ($habit, $user_id, $today, $note) {
            // 1. Créer la complétion dans HABIT_COMPLETION
            $completion = Habit_Completion::firstOrCreate(
                [
                    'habit_id' => $habit->id,
                    'user_id' => $user_id,
                    'completed_date' => $today,
                ],
                ['note' => $note]
            );

            // 2. Récupérer ou créer l'entrée dans la table STREAK
            $streak = Streak::firstOrNew(['habit_id' => $habit->id]);

            $lastDate = $streak->last_completed_date ? Carbon::parse($streak->last_completed_date) : null;

            if ($lastDate && $lastDate->isYesterday()) {
                // Si complété hier, on incrémente le streak
                $streak->current_streak += 1;
            } elseif (!$lastDate || !$lastDate->isToday()) {
                // Si c'est le premier log ou qu'on a sauté des jours, on reset à 1
                $streak->current_streak = 1;
            }

            // Initialiser best_streak à 0 s'il n'existe pas
            if (!$streak->best_streak) {
                $streak->best_streak = 0;
            }

            // Mettre à jour le record (best streak) si nécessaire
            if ($streak->current_streak > $streak->best_streak) {
                $streak->best_streak = $streak->current_streak;
            }

            $streak->last_completed_date = $today;
            $streak->save();

            return $completion;
        });
    }
}