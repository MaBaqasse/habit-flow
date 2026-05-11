<?php

use App\Models\Habit;
use App\Models\User;
use App\Services\GoogleAuthService;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

describe('GoogleCalendarService', function () {
    let $service;
    let $authService;
    let $user;
    let $habit;

    beforeEach(function () {
        Http::fake();
        Log::fake();

        $this->authService = mock(GoogleAuthService::class);
        $this->service = new GoogleCalendarService($this->authService);

        $this->user = User::factory()->create([
            'google_access_token' => 'test_token_123',
            'google_token_expires_at' => now()->addHour(),
        ]);

        $this->habit = Habit::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Morning Exercise',
            'description' => 'Daily 30-minute workout',
            'frequency' => 'daily',
            'color' => 'blue',
        ]);
    });

    describe('listCalendars', function () {
        it('lists user calendars successfully', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/users/me/calendarList' => Http::response([
                    'items' => [
                        ['id' => 'primary', 'summary' => 'Primary Calendar'],
                        ['id' => 'secondary', 'summary' => 'Secondary Calendar'],
                    ],
                ]),
            ]);

            $calendars = $this->service->listCalendars($this->user);

            expect($calendars)->toHaveCount(2);
            expect($calendars[0]['id'])->toBe('primary');
            expect($calendars[1]['id'])->toBe('secondary');
        });

        it('returns empty array when no valid token', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn(null);

            $calendars = $this->service->listCalendars($this->user);

            expect($calendars)->toBeEmpty();
        });

        it('returns empty array on API failure', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/users/me/calendarList' => Http::response([], 401),
            ]);

            $calendars = $this->service->listCalendars($this->user);

            expect($calendars)->toBeEmpty();
        });
    });

    describe('createEvent', function () {
        it('creates event successfully', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([
                    'id' => 'event_123',
                    'summary' => 'Morning Exercise',
                ]),
            ]);

            $eventId = $this->service->createEvent($this->user, $this->habit);

            expect($eventId)->toBe('event_123');

            // Vérifié que la synchronisation est enregistrée
            $this->assertDatabaseHas('calendar_syncs', [
                'user_id' => $this->user->id,
                'habit_id' => $this->habit->id,
                'google_event_id' => 'event_123',
            ]);
        });

        it('returns null when no valid token', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn(null);

            $eventId = $this->service->createEvent($this->user, $this->habit);

            expect($eventId)->toBeNull();
        });

        it('returns null on API failure', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([], 401),
            ]);

            $eventId = $this->service->createEvent($this->user, $this->habit);

            expect($eventId)->toBeNull();
        });
    });

    describe('updateEvent', function () {
        it('updates event successfully', function () {
            $sync = \App\Models\CalendarSync::create([
                'user_id' => $this->user->id,
                'habit_id' => $this->habit->id,
                'google_event_id' => 'event_123',
            ]);

            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events/event_123' => Http::response([
                    'id' => 'event_123',
                    'summary' => 'Morning Exercise Updated',
                ]),
            ]);

            $updated = $this->service->updateEvent($this->user, $this->habit);

            expect($updated)->toBeTrue();

            // Vérifié que synced_at est mis à jour
            $this->assertDatabaseHas('calendar_syncs', [
                'google_event_id' => 'event_123',
            ]);
        });

        it('returns false when sync not found', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            $updated = $this->service->updateEvent($this->user, $this->habit);

            expect($updated)->toBeFalse();
        });

        it('returns false on API failure', function () {
            $sync = \App\Models\CalendarSync::create([
                'user_id' => $this->user->id,
                'habit_id' => $this->habit->id,
                'google_event_id' => 'event_123',
            ]);

            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events/event_123' => Http::response([], 401),
            ]);

            $updated = $this->service->updateEvent($this->user, $this->habit);

            expect($updated)->toBeFalse();
        });
    });

    describe('deleteEvent', function () {
        it('deletes event successfully', function () {
            $sync = \App\Models\CalendarSync::create([
                'user_id' => $this->user->id,
                'habit_id' => $this->habit->id,
                'google_event_id' => 'event_123',
            ]);

            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events/event_123' => Http::response([]),
            ]);

            $deleted = $this->service->deleteEvent($this->user, $this->habit);

            expect($deleted)->toBeTrue();

            // Vérifié que la synchronisation est supprimée
            $this->assertDatabaseMissing('calendar_syncs', [
                'google_event_id' => 'event_123',
            ]);
        });

        it('returns false when sync not found', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            $deleted = $this->service->deleteEvent($this->user, $this->habit);

            expect($deleted)->toBeFalse();
        });

        it('returns false on API failure', function () {
            $sync = \App\Models\CalendarSync::create([
                'user_id' => $this->user->id,
                'habit_id' => $this->habit->id,
                'google_event_id' => 'event_123',
            ]);

            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events/event_123' => Http::response([], 401),
            ]);

            $deleted = $this->service->deleteEvent($this->user, $this->habit);

            expect($deleted)->toBeFalse();
        });
    });

    describe('listEvents', function () {
        it('lists events successfully', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([
                    'items' => [
                        ['id' => 'event_1', 'summary' => 'Event 1'],
                        ['id' => 'event_2', 'summary' => 'Event 2'],
                    ],
                ]),
            ]);

            $events = $this->service->listEvents($this->user);

            expect($events)->toHaveCount(2);
            expect($events[0]['id'])->toBe('event_1');
            expect($events[1]['id'])->toBe('event_2');
        });

        it('lists events with custom date range', function () {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();

            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn('test_token_123');

            Http::fake([
                'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([
                    'items' => [],
                ]),
            ]);

            $events = $this->service->listEvents($this->user, $from, $to);

            expect($events)->toBeEmpty();
        });

        it('returns empty array when no valid token', function () {
            $this->authService->shouldReceive('getValidToken')
                ->with($this->user)
                ->andReturn(null);

            $events = $this->service->listEvents($this->user);

            expect($events)->toBeEmpty();
        });
    });
});
