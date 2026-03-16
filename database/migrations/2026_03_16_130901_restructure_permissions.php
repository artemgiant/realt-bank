<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Permissions to remove (no implementation in codebase).
     */
    private array $permissionsToRemove = [
        // Deals — нет модели, контроллера, views
        'deals.view',
        'deals.create',
        'deals.edit',
        'deals.delete',
        'deals.export',
        'deals.view_all',
        'deals.close',
        'deals.reassign',
        // Reports — нет контроллера, views
        'reports.view',
        'reports.export',
        'reports.view_all',
        'reports.financial',
        'reports.analytics',
        // Не реализовано
        'properties.bulk_edit',
        'properties.export',
        'contacts.view',
        'contacts.create',
        'contacts.edit',
        'contacts.delete',
        'contacts.view_all',
        'contacts.import',
        'contacts.export',
    ];

    /**
     * New permissions to add (existing features without permissions).
     */
    private array $permissionsToAdd = [
        'properties' => [
            'properties.reassign' => 'Смена агента у объекта',
        ],
        'companies' => [
            'companies.view' => 'Просмотр компаний',
            'companies.manage' => 'Управление компаниями',
        ],
        'employees' => [
            'employees.view' => 'Просмотр сотрудников',
            'employees.manage' => 'Управление сотрудниками',
        ],
        'complexes' => [
            'complexes.view' => 'Просмотр комплексов',
            'complexes.manage' => 'Управление комплексами',
        ],
        'developers' => [
            'developers.view' => 'Просмотр девелоперов',
            'developers.manage' => 'Управление девелоперами',
        ],
        'settings' => [
            'settings.locations.manage' => 'Управление локациями',
        ],
    ];

    /**
     * Role-permission matrix after restructuring.
     */
    private function getRolePermissions(): array
    {
        return [
            'super_admin' => '*', // all permissions
            'system_admin' => [
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
            ],
            'agency_director' => [
                'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
                'properties.view_all', 'properties.reassign', 'properties.archive', 'properties.restore',
                'companies.view', 'companies.manage',
                'employees.view', 'employees.manage',
                'complexes.view', 'complexes.manage',
                'developers.view', 'developers.manage',
                'settings.view', 'settings.company.manage',
                'settings.dictionaries.manage', 'settings.locations.manage',
            ],
            'agency_admin' => [
                'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
                'properties.view_all', 'properties.reassign', 'properties.archive', 'properties.restore',
                'companies.view', 'companies.manage',
                'employees.view', 'employees.manage',
                'complexes.view', 'complexes.manage',
                'developers.view', 'developers.manage',
                'settings.dictionaries.manage', 'settings.locations.manage',
            ],
            'office_director' => [
                'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
                'properties.view_all', 'properties.reassign', 'properties.archive', 'properties.restore',
                'companies.view',
                'employees.view', 'employees.manage',
                'complexes.view',
                'developers.view',
                'settings.view',
            ],
            'office_admin' => [
                'properties.view', 'properties.create', 'properties.edit',
                'properties.view_all', 'properties.archive',
                'companies.view',
                'employees.view',
                'complexes.view',
                'developers.view',
            ],
            'team_manager' => [
                'properties.view', 'properties.create', 'properties.edit',
                'properties.view_all', 'properties.reassign',
                'companies.view',
                'employees.view',
                'complexes.view',
                'developers.view',
            ],
            'agent' => [
                'properties.view', 'properties.create', 'properties.edit',
                'companies.view',
                'complexes.view',
                'developers.view',
            ],
        ];
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Очищаем кеш
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Удаляем старые permissions (каскад удалит записи из role_has_permissions)
        Permission::whereIn('name', $this->permissionsToRemove)->delete();

        // 2. Создаём новые permissions
        foreach ($this->permissionsToAdd as $group => $perms) {
            foreach ($perms as $name => $displayName) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'group' => $group,
                        'display_name' => $displayName,
                    ]
                );
            }
        }

        // 3. Синхронизируем роли с новой матрицей
        $rolePermissions = $this->getRolePermissions();
        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if (!$role) {
                continue;
            }

            if ($permissions === '*') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($permissions);
            }
        }

        // 4. Очищаем кеш ещё раз
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Удаляем добавленные permissions
        $addedNames = [];
        foreach ($this->permissionsToAdd as $perms) {
            $addedNames = array_merge($addedNames, array_keys($perms));
        }
        Permission::whereIn('name', $addedNames)->delete();

        // Восстанавливаем удалённые permissions
        $oldPermissions = [
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
            'reports' => [
                'reports.view' => 'Просмотр отчётов',
                'reports.export' => 'Экспорт отчётов',
                'reports.view_all' => 'Отчёты по всем сотрудникам',
                'reports.financial' => 'Финансовые отчёты',
                'reports.analytics' => 'Аналитика',
            ],
            'properties' => [
                'properties.bulk_edit' => 'Массовое редактирование',
                'properties.export' => 'Экспорт данных',
            ],
            'contacts' => [
                'contacts.import' => 'Импорт клиентов',
                'contacts.export' => 'Экспорт клиентов',
            ],
        ];

        foreach ($oldPermissions as $group => $perms) {
            foreach ($perms as $name => $displayName) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'group' => $group,
                        'display_name' => $displayName,
                    ]
                );
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
