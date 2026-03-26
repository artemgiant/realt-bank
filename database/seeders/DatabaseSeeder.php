<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Довідники (без залежностей)
            CurrencySeeder::class,
            SourceSeeder::class,
            DictionarySeeder::class,

            // Географія (Country -> Region)
            CountrySeeder::class,

            // Співробітники
            EmployeeSeeder::class,

            // Компанії
            CompanySeeder::class,

            // Ролі та дозволи
            RoleSeeder::class,
            PermissionSeeder::class,

            // Адмін-користувач
            AdminUserSeeder::class,
        ]);
    }
}
