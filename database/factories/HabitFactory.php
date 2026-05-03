<?php

namespace Database\Factories;

use App\Models\Habit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habit>
 */
class HabitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Méditer', 'Boire 2L d\'eau', 'Lire 10 pages', 'Sport', 'Révision Laravel']),
            'description' => $this->faker->sentence(),
            'frequency' => \App\Models\Habit::FREQUENCY_DAILY, // Utilisation de ta constante
            'color' => $this->faker->hexColor(),
            'is_active' => true,
            'user_id' => \App\Models\User::factory(), // Crée un utilisateur si aucun n'est fourni
        ];
    }
}
