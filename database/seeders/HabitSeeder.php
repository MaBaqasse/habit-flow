<?php

namespace Database\Seeders;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Seeder;

class HabitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrFail();

        echo 'Seeding habits for user: '.$user->email."\n";

        Habit::factory()
            ->count(5)
            ->create([
                'user_id' => $user->id,
            ]);
    }
}
