<?php

use App\Models\CalendarSync;
use App\Models\Categorie;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

describe('Habit Google Calendar sync', function () {
    uses(TestCase::class, RefreshDatabase::class);

    it('creates a Google Calendar event when a habit is created with sync enabled', function () {
        $user = User::factory()->create([
            'google_access_token' => 'token_abc',
            'google_token_expires_at' => now()->addHour(),
            'google_calendar_sync_enabled' => true,
        ]);

        Http::fake([
            'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([
                'id' => 'gcal_event_123',
            ], 200),
        ]);

        $category = Categorie::create(['name' => 'Sport', 'color' => '#4A90E2']);

        $response = $this->actingAs($user)
            ->post(route('habits.store'), [
                'name' => 'Yoga',
                'description' => 'Daily stretching',
                'category_id' => $category->id,
                'frequency' => 'daily',
                'color' => '#4A90E2',
                'is_active' => '1',
                'sync_to_google_calendar' => '1',
            ]);

        $response->assertRedirect(route('habits.index'));

        $this->assertDatabaseHas('habits', [
            'name' => 'Yoga',
            'sync_to_google_calendar' => true,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('calendar_syncs', [
            'google_event_id' => 'gcal_event_123',
        ]);
    });

    it('creates or updates a Google Calendar event on habit completion when sync is enabled', function () {
        $user = User::factory()->create([
            'google_access_token' => 'token_abc',
            'google_token_expires_at' => now()->addHour(),
            'google_calendar_sync_enabled' => true,
        ]);

        $habit = Habit::factory()->create([
            'name' => 'Journal',
            'description' => 'Write a daily journal entry',
            'frequency' => 'daily',
            'color' => '#4A90E2',
            'is_active' => true,
            'sync_to_google_calendar' => true,
            'user_id' => $user->id,
        ]);

        Http::fake([
            'https://www.googleapis.com/calendar/v3/calendars/primary/events' => Http::response([
                'id' => 'gcal_event_456',
            ], 200),
        ]);

        $response = $this->actingAs($user)
            ->post(route('habits.complete', $habit), ['note' => 'Great progress']);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('calendar_syncs', [
            'habit_id' => $habit->id,
            'user_id' => $user->id,
            'google_event_id' => 'gcal_event_456',
        ]);
    });

    it('deletes the Google Calendar event when the habit is deleted', function () {
        $user = User::factory()->create([
            'google_access_token' => 'token_abc',
            'google_token_expires_at' => now()->addHour(),
            'google_calendar_sync_enabled' => true,
        ]);

        $habit = Habit::factory()->create([
            'name' => 'Meditation',
            'description' => 'Meditate daily',
            'frequency' => 'daily',
            'color' => '#4A90E2',
            'is_active' => true,
            'sync_to_google_calendar' => true,
            'user_id' => $user->id,
        ]);

        CalendarSync::create([
            'user_id' => $user->id,
            'habit_id' => $habit->id,
            'google_event_id' => 'gcal_event_789',
            'synced_at' => now(),
        ]);

        Http::fake([
            'https://www.googleapis.com/calendar/v3/calendars/primary/events/gcal_event_789' => Http::response([], 204),
        ]);

        $response = $this->actingAs($user)
            ->delete(route('habits.destroy', $habit));

        $response->assertRedirect(route('habits.index'));

        $this->assertDatabaseMissing('calendar_syncs', [
            'google_event_id' => 'gcal_event_789',
        ]);
        $this->assertDatabaseMissing('habits', ['id' => $habit->id]);
    });
});
