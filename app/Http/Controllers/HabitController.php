<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHabitRequest;
use App\Http\Requests\UpdateHabitRequest;
use App\Models\Habit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function __construct()
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
        return view('habits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHabitRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        Habit::create($validated);

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
        return view('habits.edit', compact('habit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHabitRequest $request, Habit $habit): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active', $habit->is_active);

        $habit->update($validated);

        return redirect()->route('habits.index')
            ->with('success', 'Habit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Habit $habit): RedirectResponse
    {
        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Habit deleted successfully.');
    }
}
