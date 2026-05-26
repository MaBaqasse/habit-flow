<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Display the statistics page
     */
    public function index()
    {
        $userId = auth()->id();

        // Get all active habits with completions for the user
        $habits = Habit::with('completions')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        // Calculate stats for the last 7 days
        $sevenDaysData = $this->calculateSevenDaysStats($habits);

        // Calculate insights
        $insights = $this->calculateInsights($habits);

        return view('statistics.index', [
            'sevenDaysData' => $sevenDaysData,
            'insights' => $insights,
            'habits' => $habits,
        ]);
    }

    /**
     * Calculate statistics for the last 7 days
     */
    private function calculateSevenDaysStats($habits)
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $labels[] = $date->translatedFormat('ddd dd');

            // Count completions for this day across all habits
            $completionsCount = 0;
            $totalHabits = 0;

            foreach ($habits as $habit) {
                // Check if this habit should have been done on this date
                if ($this->shouldHabitBeDoneOnDate($habit, $date)) {
                    $totalHabits++;

                    // Check if it was completed
                    if ($habit->completions->contains(function ($completion) use ($date) {
                        return $completion->completed_date->isSameDay($date);
                    })) {
                        $completionsCount++;
                    }
                }
            }

            $completionRate = $totalHabits > 0 ? round(($completionsCount / $totalHabits) * 100) : 0;
            $data[] = $completionRate;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'average' => ! empty($data) ? round(array_sum($data) / count($data)) : 0,
        ];
    }

    /**
     * Check if a habit should be done on a specific date based on its frequency
     */
    private function shouldHabitBeDoneOnDate($habit, $date)
    {
        $frequency = $habit->frequency;
        $createdDate = $habit->created_at->startOfDay();

        // If habit was created after this date, it shouldn't be done
        if ($createdDate->isAfter($date)) {
            return false;
        }

        if ($frequency === 'daily') {
            return true;
        } elseif ($frequency === 'weekly') {
            // Check if the day of week matches the creation day of week
            // or at least once a week has passed
            $daysSinceCreation = $createdDate->diffInDays($date);

            return $daysSinceCreation % 7 === 0 || ($daysSinceCreation > 0 && $daysSinceCreation % 7 !== 0);
        } elseif ($frequency === 'monthly') {
            // Once a month
            return $date->day === $createdDate->day || ($date->day > $createdDate->day && $date->month === $createdDate->month);
        }

        return true;
    }

    /**
     * Calculate text-based insights
     */
    private function calculateInsights($habits)
    {
        $insights = [];

        // Find best day of week
        $bestDay = $this->findBestDayOfWeek($habits);
        $insights['bestDay'] = $bestDay;

        // Find most stable habit
        $mostStableHabit = $this->findMostStableHabit($habits);
        $insights['mostStableHabit'] = $mostStableHabit;

        // Count completions this week
        $weekCompletions = $this->countCompletionsInPeriod($habits, 'week');
        $insights['weekCompletions'] = $weekCompletions;

        // Count completions this month
        $monthCompletions = $this->countCompletionsInPeriod($habits, 'month');
        $insights['monthCompletions'] = $monthCompletions;

        // Total habits count
        $insights['totalHabits'] = $habits->count();

        return $insights;
    }

    /**
     * Find the best day of week based on completion rate
     */
    private function findBestDayOfWeek($habits)
    {
        $dayStats = [];
        $daysOfWeek = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

        for ($dayNum = 0; $dayNum < 7; $dayNum++) {
            $completionsCount = 0;
            $totalExpected = 0;

            foreach ($habits as $habit) {
                for ($i = 0; $i < 30; $i++) {
                    $date = Carbon::now()->subDays($i)->startOfDay();
                    if ($date->dayOfWeek === $dayNum) {
                        if ($this->shouldHabitBeDoneOnDate($habit, $date)) {
                            $totalExpected++;
                            if ($habit->completions->contains(function ($completion) use ($date) {
                                return $completion->completed_date->isSameDay($date);
                            })) {
                                $completionsCount++;
                            }
                        }
                    }
                }
            }

            if ($totalExpected > 0) {
                $dayStats[$daysOfWeek[$dayNum]] = round(($completionsCount / $totalExpected) * 100);
            }
        }

        if (empty($dayStats)) {
            return ['name' => 'N/A', 'rate' => 0];
        }

        $bestDay = array_key_first($dayStats);
        $bestRate = $dayStats[$bestDay];

        foreach ($dayStats as $day => $rate) {
            if ($rate > $bestRate) {
                $bestDay = $day;
                $bestRate = $rate;
            }
        }

        return [
            'name' => ucfirst($bestDay),
            'rate' => $bestRate,
        ];
    }

    /**
     * Find the most stable habit (highest completion rate in last 30 days)
     */
    private function findMostStableHabit($habits)
    {
        $habitStats = [];

        foreach ($habits as $habit) {
            $completionsCount = 0;
            $totalExpected = 0;

            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i)->startOfDay();
                if ($this->shouldHabitBeDoneOnDate($habit, $date)) {
                    $totalExpected++;
                    if ($habit->completions->contains(function ($completion) use ($date) {
                        return $completion->completed_date->isSameDay($date);
                    })) {
                        $completionsCount++;
                    }
                }
            }

            if ($totalExpected > 0) {
                $habitStats[$habit->id] = [
                    'name' => $habit->name,
                    'rate' => round(($completionsCount / $totalExpected) * 100),
                ];
            }
        }

        if (empty($habitStats)) {
            return ['name' => 'N/A', 'rate' => 0];
        }

        // Find habit with highest rate
        $mostStable = array_reduce($habitStats, function ($carry, $item) {
            if ($carry === null || $item['rate'] > $carry['rate']) {
                return $item;
            }

            return $carry;
        });

        return $mostStable;
    }

    /**
     * Count completions in a specific period
     */
    private function countCompletionsInPeriod($habits, $period)
    {
        $count = 0;

        $startDate = $period === 'week'
            ? Carbon::now()->startOfWeek()->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = Carbon::now()->endOfDay();

        foreach ($habits as $habit) {
            $count += $habit->completions->filter(function ($completion) use ($startDate, $endDate) {
                return $completion->completed_date->isBetween($startDate, $endDate);
            })->count();
        }

        return $count;
    }

    /**
     * Export user statistics as CSV
     */
    public function exportCsv()
    {
        $userId = auth()->id();
        $user = auth()->user();

        // Get all habits with completions
        $habits = Habit::with('completions')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        // Create CSV content
        $csvContent = $this->generateCsvContent($habits, $user);

        // Return CSV file download
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="statistiques_habitudes_'.date('Y-m-d').'.csv"',
        ]);
    }

    /**
     * Generate CSV content
     */
    private function generateCsvContent($habits, $user)
    {
        $csv = 'Statistiques des Habitudes - '.$user->name."\n";
        $csv .= 'Généré le : '.Carbon::now()->translatedFormat('d F Y H:i')."\n\n";

        // Summary section
        $csv .= "=== RÉSUMÉ ===\n";
        $csv .= "Nombre total d'habitudes actives,".$habits->count()."\n";

        $weekCompletions = $this->countCompletionsInPeriod($habits, 'week');
        $csv .= 'Complétions cette semaine,'.$weekCompletions."\n";

        $monthCompletions = $this->countCompletionsInPeriod($habits, 'month');
        $csv .= 'Complétions ce mois,'.$monthCompletions."\n\n";

        // Habits details section
        $csv .= "=== DÉTAIL DES HABITUDES ===\n";
        $csv .= "Nom de l'habitude,Fréquence,Nombre de complétions,Taux de complétion (30j),Date de création\n";

        foreach ($habits as $habit) {
            $completionsCount = 0;
            $totalExpected = 0;

            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i)->startOfDay();
                if ($this->shouldHabitBeDoneOnDate($habit, $date)) {
                    $totalExpected++;
                    if ($habit->completions->contains(function ($completion) use ($date) {
                        return $completion->completed_date->isSameDay($date);
                    })) {
                        $completionsCount++;
                    }
                }
            }

            $completionRate = $totalExpected > 0 ? round(($completionsCount / $totalExpected) * 100) : 0;

            $csv .= '"'.addslashes($habit->name).'"';
            $csv .= ',"'.ucfirst($habit->frequency).'"';
            $csv .= ','.$habit->completions->count();
            $csv .= ','.$completionRate.'%';
            $csv .= ',"'.$habit->created_at->translatedFormat('d F Y').'"'."\n";
        }

        // Completions history section
        $csv .= "\n=== HISTORIQUE DES COMPLÉTIONS (30 derniers jours) ===\n";
        $csv .= "Habitude,Date de complétion,Notes\n";

        $allCompletions = [];
        foreach ($habits as $habit) {
            foreach ($habit->completions->where('completed_date', '>=', Carbon::now()->subDays(30)->startOfDay()) as $completion) {
                $allCompletions[] = [
                    'habit' => $habit->name,
                    'date' => $completion->completed_date,
                    'note' => $completion->note,
                ];
            }
        }

        // Sort by date
        usort($allCompletions, function ($a, $b) {
            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        foreach ($allCompletions as $completion) {
            $csv .= '"'.addslashes($completion['habit']).'"';
            $csv .= ',"'.$completion['date']->translatedFormat('d F Y').'"';
            $csv .= ',"'.addslashes($completion['note'] ?? '').'"'."\n";
        }

        return $csv;
    }
}
