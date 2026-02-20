<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Все permissions, сгруппированные по категориям
        $permissions = [
            // ===== ОБЪЕКТЫ (properties) =====
            'properties' => [
                'properties.view' => 'Просмотр объектов',
                'properties.create' => 'Создание объектов',
                'properties.edit' => 'Редактирование объектов',
                'properties.delete' => 'Удаление объектов',
                'properties.export' => 'Экспорт данных',
                'properties.view_all' => 'Просмотр чужих объектов',
                'properties.bulk_edit' => 'Массовое редактирование',
                'properties.archive' => 'Архивирование объектов',
                'properties.restore' => 'Восстановление объектов',
            ],

            // ===== КЛИЕНТЫ (contacts) =====
            'contacts' => [
                'contacts.view' => 'Просмотр клиентов',
                'contacts.create' => 'Создание клиентов',
                'contacts.edit' => 'Редактирование клиентов',
                'contacts.delete' => 'Удаление клиентов',
                'contacts.export' => 'Экспорт клиентов',
                'contacts.view_all' => 'Просмотр чужих клиентов',
                'contacts.import' => 'Импорт клиентов',
            ],

            // ===== СДЕЛКИ (deals) =====
            'deals' => [
                'deals.view' => 'Просмотр сделок',
                'deals.create' => 'Создание сделок',
                'deals.edit' => 'Редактирование сделок',
                'deals.delete' => 'Удаление сделок',
                'deals.export' => 'Экспорт сделок',
                'deals.view_all' => 'Просмотр чужих сделок',
                'deals.close' => 'Закрытие сделок',
                'deals.reassign' => 'Переназначение сделок',
            ],

            // ===== ОТЧЁТЫ (reports) =====
            'reports' => [
                'reports.view' => 'Просмотр отчётов',
                'reports.export' => 'Экспорт отчётов',
                'reports.view_all' => 'Отчёты по всем сотрудникам',
                'reports.financial' => 'Финансовые отчёты',
                'reports.analytics' => 'Аналитика',
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
        // Super Admin — все права
        $superAdmin = Role::findByName('super_admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        // System Admin — технические настройки, без бизнес-данных
        $systemAdmin = Role::findByName('system_admin', 'web');
        $systemAdmin->syncPermissions([
            'properties.view',
            'contacts.view',
            'reports.view',
            'settings.view',
            'settings.users.manage',
            'settings.roles.manage',
            'settings.permissions.manage',
            'settings.integrations.manage',
            'settings.dictionaries.manage',
        ]);

        // Agency Director — полный доступ к бизнес-данным
        $agencyDirector = Role::findByName('agency_director', 'web');
        $agencyDirector->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.export', 'properties.view_all', 'properties.bulk_edit',
            'properties.archive', 'properties.restore',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'contacts.export', 'contacts.view_all', 'contacts.import',
            'deals.view', 'deals.create', 'deals.edit', 'deals.delete',
            'deals.export', 'deals.view_all', 'deals.close', 'deals.reassign',
            'reports.view', 'reports.export', 'reports.view_all',
            'reports.financial', 'reports.analytics',
            'settings.view', 'settings.company.manage',
        ]);

        // Agency Admin — управление CRM-базой
        $agencyAdmin = Role::findByName('agency_admin', 'web');
        $agencyAdmin->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.export', 'properties.view_all', 'properties.bulk_edit',
            'properties.archive', 'properties.restore',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'contacts.export', 'contacts.view_all', 'contacts.import',
            'deals.view', 'deals.create', 'deals.edit', 'deals.delete',
            'deals.export', 'deals.view_all', 'deals.close', 'deals.reassign',
            'reports.view', 'reports.export', 'reports.view_all', 'reports.analytics',
            'settings.view', 'settings.dictionaries.manage',
        ]);

        // Office Director — управление офисом
        $officeDirector = Role::findByName('office_director', 'web');
        $officeDirector->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'properties.export', 'properties.view_all', 'properties.bulk_edit',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'contacts.export', 'contacts.view_all',
            'deals.view', 'deals.create', 'deals.edit', 'deals.delete',
            'deals.export', 'deals.view_all', 'deals.close', 'deals.reassign',
            'reports.view', 'reports.export', 'reports.view_all', 'reports.analytics',
            'settings.view',
        ]);

        // Office Admin — операционная поддержка
        $officeAdmin = Role::findByName('office_admin', 'web');
        $officeAdmin->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'properties.view_all', 'properties.archive',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.view_all',
            'deals.view', 'deals.create', 'deals.edit', 'deals.view_all',
            'reports.view',
            'settings.view',
        ]);

        // Team Manager — группа агентов
        $teamManager = Role::findByName('team_manager', 'web');
        $teamManager->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'properties.view_all', 'properties.bulk_edit',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.view_all',
            'deals.view', 'deals.create', 'deals.edit', 'deals.view_all', 'deals.reassign',
            'reports.view', 'reports.view_all',
        ]);

        // Agent — риелтор
        $agent = Role::findByName('agent', 'web');
        $agent->syncPermissions([
            'properties.view', 'properties.create', 'properties.edit',
            'contacts.view', 'contacts.create', 'contacts.edit',
            'deals.view', 'deals.create', 'deals.edit',
            'reports.view',
        ]);
    }
}
