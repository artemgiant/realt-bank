<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Абсолютный контроль над всеми разделами, правами, офисами, статистикой',
                'type' => 'admin',
            ],
            [
                'name' => 'system_admin',
                'display_name' => 'System Admin',
                'description' => 'Техническая часть CRM: роли, настройка полей, API, интеграции',
                'type' => 'admin',
            ],
            [
                'name' => 'agency_director',
                'display_name' => 'Agency Director',
                'description' => 'Стратегия, финансы, структура офисов. Полный обзор всех офисов и отчётов',
                'type' => 'manager',
            ],
            [
                'name' => 'agency_admin',
                'display_name' => 'Agency Admin',
                'description' => 'Управляет CRM-базой: объекты, сделки, распределение лидов, проверка качества',
                'type' => 'manager',
            ],
            [
                'name' => 'office_director',
                'display_name' => 'Office Director',
                'description' => 'Управляет офисом: агенты, отделы, планы, аналитика в рамках офиса',
                'type' => 'manager',
            ],
            [
                'name' => 'office_admin',
                'display_name' => 'Office Admin',
                'description' => 'Операционная поддержка: звонки, документы, корректность CRM',
                'type' => 'agent',
            ],
            [
                'name' => 'team_manager',
                'display_name' => 'Team Manager',
                'description' => 'Управляет группой агентов, контролирует планы, перераспределяет лиды',
                'type' => 'agent',
            ],
            [
                'name' => 'agent',
                'display_name' => 'Agent',
                'description' => 'Риелтор: работа с клиентами, объектами, сделками и лидами',
                'type' => 'agent',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name'], 'guard_name' => 'web'],
                [
                    'name' => $roleData['name'],
                    'guard_name' => 'web',
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'type' => $roleData['type'],
                ]
            );
        }
    }
}
