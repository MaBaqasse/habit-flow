<?php

namespace App\Http\Controllers;

use App\Models\CalendarSync;
use App\Models\Habit;
use App\Services\GoogleCalendarService;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitCompletionController extends Controller
{
    /**
     * Enregistre le check-in quotidien et met à jour les streaks.
     */
    public function store(Request $request, Habit $habit, StreakService $streakService, GoogleCalendarService $googleCalendarService)
    {
        // 1. Sécurité : Vérifier que l'habitude appartient bien à l'utilisateur connecté
        if ($habit->user_id !== Auth::id()) {
            abort(403, "Vous n'êtes pas autorisé à modifier cette habitude.");
        }

        if (! $habit->is_active) {
            abort(403, 'Impossible de compléter une habitude archivée.');
        }

        // 2. Optionnel : Valider la note si elle est fournie
        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        // 3. Appel au service pour gérer la logique complexe (HABIT_COMPLETION + STREAK)
        $streakService->recordCompletion(
            $habit,
            Auth::id(),
            $request->note
        );

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

        // 4. Retourner vers la page précédente avec un message de succès
        return back()->with('success', 'Félicitations ! Habitude complétée et streak mis à jour.');
    }
}
