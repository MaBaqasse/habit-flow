<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\Habit_Completion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habit_Completion>
 */
class HabitCompletionFactory extends Factory
{
    protected $model = Habit_Completion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'habit_id' => Habit::factory(),
            'user_id' => null, // Will be set when creating
            'completed_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'note' => $this->faker->optional(0.7)->sentence(),
        ];
    }
}
