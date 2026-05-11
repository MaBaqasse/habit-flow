<?php

namespace App\Services;

use App\Models\CalendarSync;
use App\Models\Habit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private const CALENDAR_API_URL = 'https://www.googleapis.com/calendar/v3';

    public function __construct(private GoogleAuthService $authService) {}

    private function getAuthorizedToken(User $user): ?string
    {
        if (! $user->google_calendar_sync_enabled) {
            return null;
        }

        return $this->authService->getValidToken($user);
    }

    /**
     * Lister les calendriers de l'utilisateur
     */
    public function listCalendars(User $user): array
    {
        $token = $this->getAuthorizedToken($user);
        if (! $token) {
            return [];
        }

        try {
            $response = Http::withToken($token)
                ->get(self::CALENDAR_API_URL.'/users/me/calendarList');

            if (! $response->successful()) {
                Log::warning('Failed to list calendars', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                ]);

                return [];
            }

            return $response->json('items', []);
        } catch (\Exception $e) {
            Log::error('Error listing calendars', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Créer un événement sur Google Calendar
     */
    public function createEvent(User $user, Habit $habit): ?string
    {
        $token = $this->getAuthorizedToken($user);
        if (! $token) {
            return null;
        }

        // Utiliser le calendrier par défaut (primary)
        $calendarId = 'primary';

        // Préparer les données de l'événement
        $eventData = $this->prepareEventData($habit);

        try {
            $response = Http::withToken($token)
                ->post(self::CALENDAR_API_URL."/calendars/{$calendarId}/events", $eventData);

            if (! $response->successful()) {
                Log::warning('Failed to create event', [
                    'user_id' => $user->id,
                    'habit_id' => $habit->id,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $eventId = $response->json('id');

            // Enregistrer la synchronisation
            CalendarSync::updateOrCreate(
                ['habit_id' => $habit->id],
                [
                    'user_id' => $user->id,
                    'google_event_id' => $eventId,
                    'synced_at' => now(),
                ]
            );

            return $eventId;
        } catch (\Exception $e) {
            Log::error('Error creating event', [
                'user_id' => $user->id,
                'habit_id' => $habit->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Mettre à jour un événement existant
     */
    public function updateEvent(User $user, Habit $habit): bool
    {
        $token = $this->getAuthorizedToken($user);
        if (! $token) {
            return false;
        }

        // Récupérer la synchronisation existante
        $sync = CalendarSync::where('habit_id', $habit->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $sync || ! $sync->google_event_id) {
            return false;
        }

        $calendarId = 'primary';
        $eventId = $sync->google_event_id;
        $eventData = $this->prepareEventData($habit);

        try {
            $response = Http::withToken($token)
                ->put(self::CALENDAR_API_URL."/calendars/{$calendarId}/events/{$eventId}", $eventData);

            if (! $response->successful()) {
                Log::warning('Failed to update event', [
                    'user_id' => $user->id,
                    'habit_id' => $habit->id,
                    'event_id' => $eventId,
                    'status' => $response->status(),
                ]);

                return false;
            }

            $sync->update(['synced_at' => now()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating event', [
                'user_id' => $user->id,
                'habit_id' => $habit->id,
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Supprimer un événement
     */
    public function deleteEvent(User $user, Habit $habit): bool
    {
        $token = $this->getAuthorizedToken($user);
        if (! $token) {
            return false;
        }

        // Récupérer la synchronisation existante
        $sync = CalendarSync::where('habit_id', $habit->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $sync || ! $sync->google_event_id) {
            return false;
        }

        $calendarId = 'primary';
        $eventId = $sync->google_event_id;

        try {
            $response = Http::withToken($token)
                ->delete(self::CALENDAR_API_URL."/calendars/{$calendarId}/events/{$eventId}");

            if (! $response->successful()) {
                Log::warning('Failed to delete event', [
                    'user_id' => $user->id,
                    'habit_id' => $habit->id,
                    'event_id' => $eventId,
                    'status' => $response->status(),
                ]);

                return false;
            }

            // Supprimer l'enregistrement de synchronisation
            $sync->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting event', [
                'user_id' => $user->id,
                'habit_id' => $habit->id,
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Lister les événements pour la réconciliation
     */
    public function listEvents(User $user, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $token = $this->getAuthorizedToken($user);
        if (! $token) {
            return [];
        }

        $calendarId = 'primary';
        $from ??= now()->startOfDay();
        $to ??= now()->endOfDay();

        try {
            $response = Http::withToken($token)
                ->get(self::CALENDAR_API_URL."/calendars/{$calendarId}/events", [
                    'timeMin' => $from->toIso8601ZuluString(),
                    'timeMax' => $to->toIso8601ZuluString(),
                    'singleEvents' => true,
                    'orderBy' => 'startTime',
                    'maxResults' => 100,
                ]);

            if (! $response->successful()) {
                Log::warning('Failed to list events', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                ]);

                return [];
            }

            return $response->json('items', []);
        } catch (\Exception $e) {
            Log::error('Error listing events', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Préparer les données de l'événement pour l'API Google
     */
    private function prepareEventData(Habit $habit): array
    {
        $now = now();

        // S'assurer que nous travaillons avec la timezone configurée (ex: Africa/Casablanca)
        $timezone = config('app.timezone');

        if ($habit->target_time) {
            // On crée l'instance Carbon en forçant la Timezone de l'application
            $startTime = Carbon::parse($habit->target_time, $timezone);
            
            // On s'assure que la date est bien celle d'aujourd'hui pour le premier événement
            $startTime->setDateFrom(now($timezone));
            // la programmer pour demain, ou rester sur aujourd'hui.
            $endTime = $startTime->copy()->addHour();
        } else {
            $startTime = $now->copy()->timezone($timezone);
            $endTime = $startTime->copy()->addHour();
        }

        return [
            'summary' => $habit->name,
            'description' => $habit->description,
            'colorId' => $this->mapHabitColorToCalendarColor($habit->color),
            'start' => [
                // Utilisation de toRfc3339String() pour inclure le fuseau horaire (+01:00)
                'dateTime' => $startTime->toRfc3339String(), 
                'timeZone' => $timezone,
            ],
            'end' => [
                'dateTime' => $endTime->toRfc3339String(),
                'timeZone' => $timezone,
            ],
            'recurrence' => $this->getRecurrenceRule($habit->frequency),
            'reminders' => [
                'useDefault' => true,
            ],
        ];
    }

    /**
     * Obtenir la règle de récurrence basée sur la fréquence
     */
    private function getRecurrenceRule(string $frequency): array
    {
        return match ($frequency) {
            'daily' => ['RRULE:FREQ=DAILY'],
            'weekly' => ['RRULE:FREQ=WEEKLY'],
            'monthly' => ['RRULE:FREQ=MONTHLY'],
            default => [],
        };
    }

    /**
     * Mapper la couleur de l'habitude à un ID de couleur Google Calendar
     * Google Calendar utilise des IDs 1-11 pour les couleurs
     */
    private function mapHabitColorToCalendarColor(?string $color): string
    {
        $colorMap = [
            'red' => '11',
            'blue' => '1',
            'green' => '2',
            'yellow' => '5',
            'purple' => '3',
            'orange' => '6',
            'cyan' => '7',
            'gray' => '8',
        ];

        return $colorMap[$color] ?? '1'; // Défaut bleu
    }
}
