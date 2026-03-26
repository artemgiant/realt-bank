<?php

namespace Database\Seeders;

use App\Models\Employee\Employee;
use App\Models\Reference\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'faktor-69c44c075dfac')->first();

        // Супер-адмін
        $user = User::updateOrCreate(
            ['email' => 'admin@faktor24.com'],
            [
                'name' => 'Админ Головенко',
                'email' => 'admin@faktor24.com',
                'phone' => '+38 (095) 090-22-93',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );

        $user->assignRole('super_admin');

        // Співробітник
        Employee::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'company_id' => $company?->id,
                'first_name' => 'Админ',
                'last_name' => 'Головенко',
                'phone' => '+38 (095) 090-22-93',
                'is_active' => true,
            ]
        );
    }
}
