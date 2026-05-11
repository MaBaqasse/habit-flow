<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHabitRequest;
use App\Http\Requests\UpdateHabitRequest;
use App\Models\CalendarSync;
use App\Models\Categorie;
use App\Models\Habit;
use App\Services\GoogleCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function __construct(public GoogleCalendarService $googleCalendarService)
    {
        $this->authorizeResource(Habit::class, 'habit');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $habits = auth()->user()->habits()->active()->get();

        return view('habits.index', compact('habits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Récupérer les catégories pour le menu déroulant
        $categories = Categorie::all();

        return view('habits.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHabitRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sync_to_google_calendar'] = $request->boolean('sync_to_google_calendar', false);

        // 3. Création de l'habitude dans la table HABIT
        $habit = Habit::create($validated);

        // 4. Initialisation automatique du Streak pour cette nouvelle habitude
        $habit->streak()->create([
            'current_streak' => 0,
            'best_streak' => 0,
            'last_completed_date' => null,
        ]);

        if ($habit->sync_to_google_calendar && auth()->user()->google_calendar_sync_enabled) {
            $this->googleCalendarService->createEvent(auth()->user(), $habit);
        }

        return redirect()->route('habits.index')
            ->with('success', 'Habit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Habit $habit): View
    {
        return view('habits.show', compact('habit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Habit $habit): View
    {
        // 1. Récupérer toutes les catégories pour remplir le menu déroulant
        $categories = Categorie::all();

        // 2. Envoyer l'habitude ET les catégories à la vue
        return view('habits.edit', compact('habit', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHabitRequest $request, Habit $habit): RedirectResponse
    {
        $wasSyncEnabled = $habit->sync_to_google_calendar;
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sync_to_google_calendar'] = $request->boolean('sync_to_google_calendar');

        $habit->update($validated);

        if ($habit->sync_to_google_calendar && auth()->user()->google_calendar_sync_enabled) {
            $existingSync = CalendarSync::where('habit_id', $habit->id)
                ->where('user_id', auth()->id())
                ->exists();

            if ($existingSync) {
                $this->googleCalendarService->updateEvent(auth()->user(), $habit);
            } else {
                $this->googleCalendarService->createEvent(auth()->user(), $habit);
            }
        }

        if (! $habit->sync_to_google_calendar && $wasSyncEnabled) {
            $this->googleCalendarService->deleteEvent(auth()->user(), $habit);
        }

        // Si l'habitude vient d'être désactivée, on peut rediriger vers les archives
        if (! $habit->is_active) {
            return redirect()->route('habits.archives')
                ->with('success', 'Habitude archivée avec succès.');
        }

        return redirect()->route('habits.index')
            ->with('success', 'Habit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Habit $habit): RedirectResponse
    {
        if (CalendarSync::where('habit_id', $habit->id)->where('user_id', auth()->id())->exists()) {
            $this->googleCalendarService->deleteEvent(auth()->user(), $habit);
        }

        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Habit deleted successfully.');
    }

    /**
     * Affiche les habitudes archivées (inactives).
     */
    public function archives(): View
    {
        $archivedHabits = auth()->user()->habits()
            ->archived()
            ->with(['category', 'streak'])
            ->get();

        return view('habits.archives', compact('archivedHabits'));
    }
}
