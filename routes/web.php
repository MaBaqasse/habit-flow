<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitCompletionController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitStatsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationPreferenceController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Dashboard géré par le nouveau DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/habits/archives', [HabitController::class, 'archives'])->name('habits.archives');

    // Gestion des Habitudes (CRUD) - Exclude show car on utilise HabitStatsController
    Route::resource('habits', HabitController::class)->except(['show']);

    // Statistiques détaillées d'une habitude
    Route::get('habits/{habit}', [HabitStatsController::class, 'show'])->name('habits.show');

    // Enregistrement d'une complétion (Check-in)
    Route::post('habits/{habit}/complete', [HabitCompletionController::class, 'store'])->name('habits.complete');

    Route::get('auth/google', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google.redirect');

    Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');

    Route::post('auth/google/disconnect', [GoogleAuthController::class, 'disconnect'])
        ->name('auth.google.disconnect');

        // Paramètres de notification
        Route::get('/settings/notifications', [NotificationPreferenceController::class, 'edit'])->name('settings.notifications.edit');
        Route::patch('/settings/notifications', [NotificationPreferenceController::class, 'update'])->name('settings.notifications.update');

    });
Route::get('/settings/notifications', [NotificationPreferenceController::class, 'edit'])->name('settings.notifications.edit');
Route::patch('/settings/notifications', [NotificationPreferenceController::class, 'update'])->name('settings.notifications.update');

require __DIR__.'/auth.php';
