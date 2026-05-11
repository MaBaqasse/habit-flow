<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Habit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $selectedCategoryId = $request->query('category');

        // 1. Eager Loading pour éviter le problème N+1
        $habitsQuery = Habit::with(['category', 'streak', 'completions'])
            ->where('user_id', $userId)
            ->where('is_active', true);

        // Application du filtre de catégorie si présent
        if ($selectedCategoryId) {
            $habitsQuery->where('category_id', $selectedCategoryId);
        }

        $habits = $habitsQuery->get()->map(function (Habit $habit) {
            $habit->completed_current_period = $this->isHabitCompletedForCurrentPeriod($habit);
            $habit->is_due = ! $habit->completed_current_period;

            return $habit;
        });

        // 2. Calcul du taux de complétion global (indépendant du filtre)
        // 1. Charger toutes les habitudes actives pour les stats (indépendant du filtre)
        $allActiveHabits = Habit::with('completions')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        // 2. Filtrer pour obtenir uniquement les habitudes qui sont attendues (Dues)
        $habitsDue = $allActiveHabits->filter(function ($habit) {
            // Une habitude est "Due" si elle n'est pas encore complétée pour sa période actuelle
            return ! $this->isHabitCompletedForCurrentPeriod($habit);
        });

        // 3. Nombre d'habitudes complétées spécifiquement aujourd'hui
        $completedToday = $allActiveHabits->filter(function ($habit) {
            return $habit->completions->contains(function ($completion) {
                return $completion->completed_date->isSameDay(Carbon::today());
            });
        })->count();

        // 4. Calcul du taux : (Faites aujourd'hui) / (Restantes à faire + Faites aujourd'hui)
        // Cela garantit que les habitudes hebdo/mensuelles déjà faites ne pénalisent pas le score.
        $totalPotentiel = $habitsDue->count() + $completedToday;

        $dailyCompletionRate = $totalPotentiel > 0
            ? round(($completedToday / $totalPotentiel) * 100)
            : 0;

        // 3. Récupérer le Top 3 des streaks actifs
        $topStreaks = Habit::with(['category', 'streak'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->get()
            ->sortByDesc(fn ($habit) => optional($habit->streak)->current_streak ?? 0)
            ->take(3);

        $categories = Categorie::all();
        $selectedCategory = $selectedCategoryId;

        return view('dashboard', compact(
            'habits',
            'dailyCompletionRate',
            'completedToday',
            'topStreaks',
            'categories',
            'selectedCategory'
        ));
    }

    private function isHabitCompletedForCurrentPeriod(Habit $habit): bool
    {
        $now = Carbon::now();

        $periodStart = match ($habit->frequency) {
            Habit::FREQUENCY_DAILY => Carbon::today(),
            Habit::FREQUENCY_WEEKLY => $now->copy()->startOfWeek(),
            Habit::FREQUENCY_MONTHLY => $now->copy()->startOfMonth(),
            default => Carbon::today(),
        };

        return $habit->completions->contains(function ($completion) use ($periodStart, $now) {
            return $completion->completed_date->between($periodStart, $now);
        });
    }
}
