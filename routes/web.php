<?php

use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitCompletionController;
use App\Http\Controllers\ProfileController;
use App\Models\Categorie;
use App\Models\Habit_Completion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $selectedCategory = $request->query('category');

    $categories = Categorie::orderBy('name')->get();

    $habitsQuery = $user->habits()->with(['category', 'streak'])->active();
    if ($selectedCategory) {
        $habitsQuery->where('category_id', $selectedCategory);
    }

    $habits = $habitsQuery->get();
    $completedToday = Habit_Completion::where('user_id', $user->id)
        ->where('completed_date', today())
        ->count();

    $dailyCompletionRate = $habits->count() ? round($completedToday * 100 / $habits->count()) : 0;

    return view('dashboard', compact('habits', 'categories', 'selectedCategory', 'dailyCompletionRate', 'completedToday'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('habits', HabitController::class);
    Route::post('habits/{habit}/complete', [HabitCompletionController::class, 'store'])->name('habits.complete');
});

require __DIR__.'/auth.php';
