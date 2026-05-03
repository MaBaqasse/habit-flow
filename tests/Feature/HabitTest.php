<?php

use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

test('les routes des habitudes sont protégées et redirigent vers le login pour les invités', function () {
    $habit = Habit::factory()->for($this->user)->create();

    $this->get(route('habits.index'))
        ->assertRedirect(route('login'));

    $this->get(route('habits.create'))
        ->assertRedirect(route('login'));

    $this->post(route('habits.store'), [])->assertRedirect(route('login'));

    $this->get(route('habits.show', $habit))
        ->assertRedirect(route('login'));

    $this->get(route('habits.edit', $habit))
        ->assertRedirect(route('login'));

    $this->put(route('habits.update', $habit), [])->assertRedirect(route('login'));

    $this->delete(route('habits.destroy', $habit))
        ->assertRedirect(route('login'));
});

test('store crée une habitude et redirige vers index', function () {
    $response = $this->withoutMiddleware() // Désactive temporairement le CSRF pour ce test
        ->actingAs($this->user)
        ->post(route('habits.store'), [
            'name' => 'Lire 30 minutes',
            'frequency' => Habit::FREQUENCY_DAILY,
            'color' => '#4A90E2',
            'is_active' => true,
        ]);

    $response->assertRedirect(route('habits.index'));

    $this->assertDatabaseHas('habits', [
        'user_id' => $this->user->id,
        'name' => 'Lire 30 minutes',
        'frequency' => Habit::FREQUENCY_DAILY,
        'color' => '#4A90E2',
        'is_active' => true,
    ]);
});

test('update modifie le nom et la couleur d une habitude appartenant a l utilisateur', function () {
    $habit = Habit::factory()->for($this->user)->create([
        'name' => 'Habit original',
        'color' => '#000000',
    ]);

    $response = $this->actingAs($this->user)
        ->put(route('habits.update', $habit), [
            'name' => 'Nom Modifié',
            'frequency' => Habit::FREQUENCY_MONTHLY,
            'color' => '#FF0000',
            'is_active' => '1',
        ]);

    $response->assertRedirect(route('habits.index'));

    $this->assertDatabaseHas('habits', [
        'id' => $habit->id,
        'name' => 'Nom Modifié',
        'color' => '#FF0000',
        'frequency' => Habit::FREQUENCY_MONTHLY,
    ]);
});

test('destroy supprime une habitude et la retire de la base', function () {
    $habit = Habit::factory()->for($this->user)->create();

    $response = $this->actingAs($this->user)
        ->delete(route('habits.destroy', $habit));

    $response->assertRedirect(route('habits.index'));
    $this->assertDatabaseMissing('habits', ['id' => $habit->id]);
});

test('un utilisateur ne peut pas voir, modifier ou supprimer une habitude d un autre utilisateur', function () {
    $otherHabit = Habit::factory()->for($this->otherUser)->create();

    $this->actingAs($this->user)
        ->get(route('habits.show', $otherHabit))
        ->assertForbidden();

    $this->actingAs($this->user)
        ->get(route('habits.edit', $otherHabit))
        ->assertForbidden();

    $this->actingAs($this->user)
        ->put(route('habits.update', $otherHabit), [
            'name' => 'Tentative',
            'frequency' => Habit::FREQUENCY_DAILY,
            'color' => '#123456',
            'is_active' => '1',
        ])
        ->assertForbidden();

    $this->actingAs($this->user)
        ->delete(route('habits.destroy', $otherHabit))
        ->assertForbidden();
});
