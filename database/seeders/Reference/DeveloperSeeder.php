<?php

namespace Database\Seeders\Reference;

use App\Models\Reference\Developer;
use Illuminate\Database\Seeder;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Очищаем девелоперов созданных вручную и создаем 10 новых с контактами
        Developer::factory()->cleanAndCreate(10);
    }
}
