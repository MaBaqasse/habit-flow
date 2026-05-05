<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Categorie::insert([
        ['name' => 'Sport', 'color' => '#EF4444'], // Rouge
        ['name' => 'Santé', 'color' => '#10B981'], // Vert
        ['name' => 'Productivité', 'color' => '#3B82F6'], // Bleu
        ['name' => 'Bien-être', 'color' => '#F59E0B'], // Orange
    ]);
    }
}
