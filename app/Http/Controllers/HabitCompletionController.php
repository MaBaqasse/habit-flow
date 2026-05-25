<?php

namespace App\Http\Controllers;

use App\Models\CalendarSync;
use App\Models\Habit;
use App\Services\GoogleCalendarService;
use App\Services\StreakService;
use App\Notifications\StreakReachedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitCompletionController extends Controller
{
    /**
     * Enregistre le check-in quotidien et met à jour les streaks.
     */
    public function store(Request $request, Habit $habit, StreakService $streakService, GoogleCalendarService $googleCalendarService)
    {
        // 1. Sécurité
        if ($habit->user_id !== Auth::id()) {
            abort(403, "Vous n'êtes pas autorisé à modifier cette habitude.");
        }

        if (! $habit->is_active) {
            abort(403, 'Impossible de compléter une habitude archivée.');
        }

        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        // 2. Appel au service pour enregistrer la complétion (Met à jour le streak en DB)
        $streakService->recordCompletion(
            $habit,
            Auth::id(),
            $request->note
        );

        // 3. Détection du palier de Streak & Envoi de la Notification Email
        $streak = $habit->streak; // Récupère la relation de l'habitude
        if ($streak) {
            $currentCount = $streak->current_streak;
            $user = Auth::user();
            $settings = $user->notificationSettings; // Récupère les préférences de l'image PRD

            // Si l'utilisateur a activé les alertes ET qu'on est sur un multiple de 7 (7, 14, 21...)
            if ($settings && $settings->streak_alert_enabled && $currentCount >= 7 && $currentCount % 7 == 0) {
                $user->notify(new StreakReachedNotification($habit, $currentCount));
            }
        }

        // 4. Synchronisation Google Calendar (Ton code existant)
        if ($habit->sync_to_google_calendar && Auth::user()->google_calendar_sync_enabled) {
            $existingSync = CalendarSync::where('habit_id', $habit->id)
                ->where('user_id', Auth::id())
                ->exists();

            if ($existingSync) {
                $googleCalendarService->updateEvent(Auth::user(), $habit);
            } else {
                $googleCalendarService->createEvent(Auth::user(), $habit);
            }
        }

        return back()->with('success', 'Félicitations ! Habitude complétée, streak mis à jour et notification vérifiée.');
    }
}
