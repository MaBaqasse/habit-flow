<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    public function edit()
    {
        // On récupère ou on crée à la volée les paramètres si inexistants (sécurité)
        $settings = Auth::user()->notificationSettings ?? Auth::user()->notificationSettings()->create([
            'email_reminder_enabled' => true,
            'reminder_time' => '07:00:00',
            'weekly_summary_enabled' => true,
            'weekly_summary_day' => 6,
            'weekly_summary_time' => '18:00:00',
            'streak_alert_enabled' => true,
            'email_digest_format' => 'summary',
        ]);

        return view('settings.notifications', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'reminder_time' => 'required|date_format:H:i',
            'weekly_summary_day' => 'required|integer|between:0,6',
            'weekly_summary_time' => 'required|date_format:H:i',
            'email_digest_format' => 'required|in:summary,detailed,minimal',
        ]);

        $user->notificationSettings()->update([
            'email_reminder_enabled' => $request->boolean('email_reminder_enabled'),
            'weekly_summary_enabled' => $request->boolean('weekly_summary_enabled'),
            'streak_alert_enabled' => $request->boolean('streak_alert_enabled'),
            'reminder_time' => $validated['reminder_time'],
            'weekly_summary_day' => $validated['weekly_summary_day'],
            'weekly_summary_time' => $validated['weekly_summary_time'],
            'email_digest_format' => $validated['email_digest_format'],
        ]);

        return back()->with('success', 'Vos préférences de notification ont été mises à jour.');
    }
}
