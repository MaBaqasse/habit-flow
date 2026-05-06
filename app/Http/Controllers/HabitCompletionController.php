<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habit;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;

class HabitCompletionController extends Controller
{
    /**
     * Enregistre le check-in quotidien et met à jour les streaks.
     */
    public function store(Request $request, Habit $habit, StreakService $streakService)
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

        // 4. Retourner vers la page précédente avec un message de succès
        return back()->with('success', 'Félicitations ! Habitude complétée et streak mis à jour.');
    }
}
