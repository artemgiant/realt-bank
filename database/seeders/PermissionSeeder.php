<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Очищаем кеш permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Все permissions, сгруппированные по категориям (28 шт.)
        $permissions = [
            // ===== ОБЪЕКТЫ (properties) =====
            'properties' => [
                'properties.view' => 'Просмотр объектов',
                'properties.create' => 'Создание объектов',
                'properties.edit' => 'Редактирование объектов',
                'properties.delete' => 'Удаление объектов',
                'properties.view_all' => 'Просмотр чужих объектов',
                'properties.reassign' => 'Смена агента у объекта',
                'properties.archive' => 'Архивирование объектов',
                'properties.restore' => 'Восстановление объектов',
            ],

            // ===== КОМПАНИИ (companies) =====
            'companies' => [
                'companies.view' => 'Просмотр компаний',
                'companies.manage' => 'Управление компаниями',
            ],

            // ===== СОТРУДНИКИ (employees) =====
            'employees' => [
                'employees.view' => 'Просмотр сотрудников',
                'employees.manage' => 'Управление сотрудниками',
            ],

            // ===== КОМПЛЕКСЫ (complexes) =====
            'complexes' => [
                'complexes.view' => 'Просмотр комплексов',
                'complexes.manage' => 'Управление комплексами',
            ],

            // ===== ДЕВЕЛОПЕРЫ (developers) =====
            'developers' => [
                'developers.view' => 'Просмотр девелоперов',
                'developers.manage' => 'Управление девелоперами',
            ],

            // ===== НАСТРОЙКИ (settings) =====
            'settings' => [
                'settings.view' => 'Просмотр настроек',
                'settings.users.manage' => 'Управление пользователями',
                'settings.roles.manage' => 'Управление ролями',
                'settings.permissions.manage' => 'Управление правами',
                'settings.company.manage' => 'Настройки компании',
                'settings.integrations.manage' => 'Интеграции',
                'settings.dictionaries.manage' => 'Справочники',
                'settings.locations.manage' => 'Управление локациями',
            ],
        ];

        // Создаём permissions
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $name => $displayName) {
                Permission::updateOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'name' => $name,
                        'guard_name' => 'web',
                        'group' => $group,
                        'display_name' => $displayName,
                    ]
                );
            }
        }

        // Назначаем дефолтные permissions для ролей
        $this->assignDefaultPermissions();
    }

    /**
     * Assign default permissions to roles.
     */
    private function assignDefaultPermissions(): void
    {
        // Super Admin — все права (+ Gate::before обходит проверки)
        $superAdmin = Role::findByName('super_admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        // System Admin — технические настройки, read-only для бизнес-данных
        $systemAdmin = Role::findByName('system_admin', 'web');
        $systemAdmin->syncPermissions([
            'properties.view', 'properties.view_all',
            'companies.view',
            'employees.view',
            'complexes.view',
            'developers.view',
            'settings.view',
            'settings.users.manage',
            'settings.roles.manage',
            'settings.permissions.manage',
            'settings.integrations.manage',
            'settings.dictionaries.manage',
            'settings.locations.manage',
        ]);

        // Agency Director — полный доступ к бизнес-данным + настройки компании
        $agencyDirector = Role::findByName('agency_director', 'web');
        $agencyDirector->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.view_all', 'properties.reassign', 'properties.archive', 'properties.restore',
            'companies.view', 'companies.manage',
            'employees.view', 'employees.manage',
            'complexes.view', 'complexes.manage',
            'developers.view', 'developers.manage',
            'settings.view', 'settings.company.manage',
            'settings.dictionaries.manage', 'settings.locations.manage',
        ]);

        // Agency Admin — управление CRM-базой
        $agencyAdmin = Role::findByName('agency_admin', 'web');
        $agencyAdmin->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.view_all', 'properties.reassign', 'properties.archive', 'properties.restore',
            'companies.view', 'companies.manage',
            'employees.view', 'employees.manage',
            'complexes.view', 'complexes.manage',
            'developers.view', 'developers.manage',
            'settings.dictionaries.manage', 'settings.locations.manage',
        ]);

        // Office Director — управление офисом
        $officeDirector = Role::findByName('office_director', 'web');
        $officeDirector->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.view_all', 'properties.reassign', 'properties.archive', 'properties.restore',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'contacts.view_all',
            'companies.view',
            'employees.view', 'employees.manage',
            'complexes.view',
            'developers.view',
            'settings.view',
        ]);

        // Office Admin — операционная поддержка
        $officeAdmin = Role::findByName('office_admin', 'web');
        $officeAdmin->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'properties.view_all', 'properties.archive',
            'companies.view',
            'employees.view',
            'complexes.view',
            'developers.view',
        ]);

        // Team Manager — группа агентов
        $teamManager = Role::findByName('team_manager', 'web');
        $teamManager->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'properties.view_all', 'properties.reassign',
            'companies.view',
            'employees.view',
            'complexes.view',
            'developers.view',
        ]);

        // Agent — риелтор
        $agent = Role::findByName('agent', 'web');
        $agent->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'companies.view',
            'complexes.view',
            'developers.view',
        ]);
    }
}
