<?php

use App\Models\Habit;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Http;

describe('GoogleCalendarService target_time', function () {
    it('uses target_time when preparing event data', function () {
        $user = User::factory()->create([
            'google_access_token' => 'token_abc',
            'google_token_expires_at' => now()->addHour(),
            'google_calendar_sync_enabled' => true,
        ]);

        Http::fake([
            'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([
                'id' => 'gcal_event_789',
            ], 200),
        ]);

        $habit = Habit::factory()->create([
            'name' => 'Morning Run',
            'description' => 'Run at 08:00 AM',
            'frequency' => 'daily',
            'target_time' => '08:00',
            'color' => '#4A90E2',
            'is_active' => true,
            'sync_to_google_calendar' => true,
            'user_id' => $user->id,
        ]);

        $service = app(GoogleCalendarService::class);
        $eventId = $service->createEvent($user, $habit);

        expect($eventId)->toBe('gcal_event_789');

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            // Vérifier que l'heure commence à 08:00
            expect($body['start']['dateTime'])->toContain('08:00');
            expect($body['end']['dateTime'])->toContain('09:00');

            return true;
        });
    });

    it('falls back to current time when target_time is null', function () {
        $user = User::factory()->create([
            'google_access_token' => 'token_abc',
            'google_token_expires_at' => now()->addHour(),
            'google_calendar_sync_enabled' => true,
        ]);

        Http::fake([
            'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([
                'id' => 'gcal_event_999',
            ], 200),
        ]);

        $habit = Habit::factory()->create([
            'name' => 'Evening Yoga',
            'description' => 'Yoga session',
            'frequency' => 'daily',
            'target_time' => null,
            'color' => '#4A90E2',
            'is_active' => true,
            'sync_to_google_calendar' => true,
            'user_id' => $user->id,
        ]);

        $service = app(GoogleCalendarService::class);
        $eventId = $service->createEvent($user, $habit);

        expect($eventId)->toBe('gcal_event_999');
    });
});
