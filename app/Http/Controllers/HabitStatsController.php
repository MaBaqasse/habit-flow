<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Carbon\Carbon;

class HabitStatsController extends Controller
{
    /**
     * Affiche les statistiques détaillées d'une habitude.
     */
    public function show(Habit $habit)
    {
        // Vérifier l'autorisation
        $this->authorize('view', $habit);

        // Eager loading pour éviter N+1
        $habit->load(['category', 'streak', 'completions']);

        // Calcul des stats des 30 derniers jours
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $completionsLast30Days = $habit->completions
            ->filter(fn ($c) => $c->completed_date->greaterThanOrEqualTo($thirtyDaysAgo))
            ->sortBy('completed_date');

        $totalCompletionsLast30 = $completionsLast30Days->count();
        $expectedDaysLast30 = $this->getExpectedCountForPeriod($habit->frequency, 30);
        $completionRateLast30 = $expectedDaysLast30 > 0 ? round(($totalCompletionsLast30 / $expectedDaysLast30) * 100) : 0;

        // Score de stabilité (1-10) basé sur la régularité des heures
        $stabilityScore = $this->calculateStabilityScore($completionsLast30Days);

        // Heatmap : génère les données selon la fréquence
        $heatmapData = $this->generateHeatmapData($habit, $completionsLast30Days);

        // Graphique : complétions par semaine
        $weeklyChartData = $this->generateWeeklyChartData($habit, 4); // 4 dernières semaines

        return view('habits.show', compact(
            'habit',
            'totalCompletionsLast30',
            'completionRateLast30',
            'stabilityScore',
            'heatmapData',
            'weeklyChartData'
        ));
    }

    /**
     * Calcule le nombre attendu de complétions pour une période donnée.
     */
    private function getExpectedCountForPeriod(string $frequency, int $days): int
    {
        return match ($frequency) {
            Habit::FREQUENCY_DAILY => $days,
            Habit::FREQUENCY_WEEKLY => (int) ceil($days / 7),
            Habit::FREQUENCY_MONTHLY => (int) ceil($days / 30),
            default => $days,
        };
    }

    /**
     * Calcule un score de stabilité (1-10) basé sur la régularité des heures de check-in.
     */
    private function calculateStabilityScore($completions): int
    {
        if ($completions->isEmpty()) {
            return 0;
        }

        // Extraire les heures des check-ins
        $hours = $completions->map(function ($c) {
            return $c->created_at ? $c->created_at->hour : null;
        })->filter()->values();

        if ($hours->count() < 2) {
            return 5; // Score neutre si peu de données
        }

        // Calculer l'écart-type des heures
        $avgHour = $hours->average();
        $variance = $hours->map(fn ($h) => pow($h - $avgHour, 2))->average();
        $stdDev = sqrt($variance);

        // Convertir l'écart-type en score (0 = très régulier = 10, 12+ = très irrégulier = 1)
        $score = max(1, min(10, round(10 - ($stdDev / 1.2))));

        return $score;
    }

    /**
     * Génère les données pour la heatmap selon la fréquence.
     */
    private function generateHeatmapData(Habit $habit, $completions)
    {
        $now = Carbon::now();
        $data = [];

        if ($habit->frequency === Habit::FREQUENCY_DAILY) {
            // Heatmap 30 jours
            for ($i = 29; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $isCompleted = $completions->contains(fn ($c) => $c->completed_date->isSameDay($date));
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'label' => $date->format('M d'),
                    'completed' => $isCompleted,
                ];
            }
        } else {
            // Heatmap 12 semaines pour weekly/monthly
            for ($i = 11; $i >= 0; $i--) {
                $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $isCompleted = $completions->contains(function ($c) use ($weekStart, $weekEnd) {
                    return $c->completed_date->between($weekStart, $weekEnd);
                });
                $data[] = [
                    'date' => $weekStart->format('Y-m-d'),
                    'label' => 'W'.$weekStart->format('W'),
                    'completed' => $isCompleted,
                ];
            }
        }

        return $data;
    }

    /**
     * Génère les données pour le graphique en barres par semaine.
     */
    private function generateWeeklyChartData(Habit $habit, int $weeksBack)
    {
        $now = Carbon::now();
        $labels = [];
        $data = [];

        for ($i = $weeksBack - 1; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $label = $weekStart->format('M d');
            $completionCount = $habit->completions
                ->filter(fn ($c) => $c->completed_date->between($weekStart, $weekEnd))
                ->count();

            $labels[] = $label;
            $data[] = $completionCount;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
