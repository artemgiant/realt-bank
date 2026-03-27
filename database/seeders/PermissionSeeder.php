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

        // Все permissions, сгруппированные по категориям (32 шт.)
        $permissions = [
            // ===== ОБЪЕКТЫ (properties) =====
            'properties' => [
                'properties.view' => 'Просмотр объектов',
                'properties.create' => 'Создание объектов',
                'properties.edit' => 'Редактирование объектов',
                'properties.delete' => 'Удаление объектов',
                'properties.view_office' => 'Просмотр объектов офиса',
                'properties.view_company' => 'Просмотр объектов компании',
                'properties.edit_office' => 'Редактирование объектов офиса',
                'properties.edit_company' => 'Редактирование объектов компании',
                'properties.delete_office' => 'Удаление объектов офиса',
                'properties.delete_company' => 'Удаление объектов компании',
                'properties.reassign' => 'Смена агента у объекта',
                'properties.reassign_office' => 'Смена агента (офис)',
                'properties.reassign_company' => 'Смена агента (компания)',
            ],

            // ===== КОМПАНИИ (companies) =====
            'companies' => [
                'companies.view' => 'Просмотр компаний',
                'companies.create' => 'Создание компаний',
                'companies.edit' => 'Редактирование компаний',
                'companies.delete' => 'Удаление компаний',
            ],

            // ===== СОТРУДНИКИ (employees) =====
            'employees' => [
                'employees.view' => 'Просмотр сотрудников',
                'employees.create' => 'Создание сотрудников',
                'employees.edit' => 'Редактирование сотрудников',
                'employees.delete' => 'Удаление сотрудников',
            ],

            // ===== КОМПЛЕКСЫ (complexes) =====
            'complexes' => [
                'complexes.view' => 'Просмотр комплексов',
                'complexes.create' => 'Создание комплексов',
                'complexes.edit' => 'Редактирование комплексов',
                'complexes.delete' => 'Удаление комплексов',
            ],

            // ===== ДЕВЕЛОПЕРЫ (developers) =====
            'developers' => [
                'developers.view' => 'Просмотр девелоперов',
                'developers.create' => 'Создание девелоперов',
                'developers.edit' => 'Редактирование девелоперов',
                'developers.delete' => 'Удаление девелоперов',
            ],

            // ===== НАСТРОЙКИ (settings) =====
            'settings' => [
                'settings.view' => 'Просмотр настроек',
                'settings.users.manage' => 'Управление пользователями',
                'settings.roles.manage' => 'Управление ролями',
                'settings.permissions.manage' => 'Управление правами',
                'settings.company.manage' => 'Настройки компании',
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
            'properties.view', 'properties.view_company',
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
            'properties.view_office', 'properties.view_company', 'properties.reassign',
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'complexes.view', 'complexes.create', 'complexes.edit', 'complexes.delete',
            'developers.view', 'developers.create', 'developers.edit', 'developers.delete',
            'settings.view', 'settings.company.manage',
            'settings.dictionaries.manage', 'settings.locations.manage',
        ]);

        // Agency Admin — управление CRM-базой
        $agencyAdmin = Role::findByName('agency_admin', 'web');
        $agencyAdmin->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.view_office', 'properties.view_company', 'properties.reassign',
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'complexes.view', 'complexes.create', 'complexes.edit', 'complexes.delete',
            'developers.view', 'developers.create', 'developers.edit', 'developers.delete',
            'settings.dictionaries.manage', 'settings.locations.manage',
        ]);

        // Office Director — управление офисом
        $officeDirector = Role::findByName('office_director', 'web');
        $officeDirector->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.view_office', 'properties.view_company', 'properties.reassign',
            'companies.view',
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'complexes.view',
            'developers.view',
            'settings.view',
        ]);

        // Office Admin — операционная поддержка
        $officeAdmin = Role::findByName('office_admin', 'web');
        $officeAdmin->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'properties.view_office', 'properties.view_company',
            'companies.view',
            'employees.view',
            'complexes.view',
            'developers.view',
        ]);

        // Team Manager — группа агентов
        $teamManager = Role::findByName('team_manager', 'web');
        $teamManager->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'properties.view_office', 'properties.view_company', 'properties.reassign',
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
