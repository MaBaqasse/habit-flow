<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Mail\DailyDigestMail;
use App\Mail\WeeklyReportMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Carbon;

// --- RAPPEL QUOTIDIEN (Vérification toutes les minutes) ---
Schedule::call(function () {
    // 1. On récupère l'heure actuelle au format 'H:i:00' (ex: '17:50:00')
    $currentTime = now()->timezone(config('app.timezone'))->format('H:i:00');

    // 2. On filtre les utilisateurs dont l'heure choisie correspond à la minute actuelle
    $users = User::whereHas('notificationSettings', function ($query) use ($currentTime) {
        $query->where('email_reminder_enabled', true)
              ->whereTime('reminder_time', $currentTime); // <-- Filtre dynamique de l'heure sur un champ time
    })->with(['habits' => function($query) {
        $query->where('is_active', true);
    }])->get();

    foreach ($users as $user) {
        $todaysHabits = $user->habits;

        if ($todaysHabits->isNotEmpty()) {
            Mail::to($user->email)->queue(new DailyDigestMail($user, $todaysHabits));
        }
    }
})->everyMinute(); // <-- On tourne en arrière-plan chaque minute pour vérifier s'il y a des correspondances


// --- BILAN HEBDOMADAIRE (Vérification dynamique toutes les minutes) ---
Schedule::call(function () {
    // 1. On récupère le jour actuel de la semaine (0-6) et l'heure actuelle (H:i:00)
    $currentDay = now()->timezone(config('app.timezone'))->dayOfWeek;
    $currentTime = now()->timezone(config('app.timezone'))->format('H:i:00');

    // 2. On cherche les utilisateurs qui veulent leur bilan CE JOUR et à CETTE HEURE précise
    $users = User::whereHas('notificationSettings', function ($query) use ($currentDay, $currentTime) {
        $query->where('weekly_summary_enabled', true)
              ->where('weekly_summary_day', $currentDay)
              ->whereTime('weekly_summary_time', $currentTime); // <-- Comparaison horaire exacte sur un champ time
    })->get();

    foreach ($users as $user) {
        $completedThisWeek = $user->habits()
            ->whereHas('completions', function($query) {
                $query->where('completed_at', '>=', now()->subDays(7));
            })->count();

        $stats = [
            'total_completed' => $completedThisWeek,
            'week_range' => now()->subDays(7)->format('d/m') . ' au ' . now()->format('d/m'),
        ];

        Mail::to($user->email)->queue(new WeeklyReportMail($user, $stats));
    }
})->everyMinute(); // <-- On passe à "everyMinute" pour attraper l'heure exacte de l'utilisateur



Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');