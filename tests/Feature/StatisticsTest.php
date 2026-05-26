<?php

use App\Models\User;
use App\Models\Habit;
use Database\Factories\HabitCompletionFactory;

describe('Statistics Page', function () {
    it('shows statistics page for authenticated user', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/statistics');

        $response->assertStatus(200);
        $response->assertViewIs('statistics.index');
    });

    it('displays statistics with habits and completions', function () {
        $user = User::factory()->create();
        $habit = Habit::factory()->create(['user_id' => $user->id]);

        // Create some completions
        HabitCompletionFactory::new()->create([
            'habit_id' => $habit->id,
            'user_id' => $user->id,
            'completed_date' => now()->subDays(2),
        ]);

        $response = $this->actingAs($user)->get('/statistics');

        $response->assertStatus(200);
        $response->assertViewHas(['sevenDaysData', 'insights', 'habits']);
    });

    it('exports CSV for authenticated user', function () {
        $user = User::factory()->create();
        Habit::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/statistics/export-csv');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');
    });

    it('redirects unauthenticated user to login', function () {
        $response = $this->get('/statistics');

        $response->assertRedirect('/login');
    });
});
