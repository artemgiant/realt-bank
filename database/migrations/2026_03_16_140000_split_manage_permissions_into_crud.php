<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Split *.manage into *.create, *.edit, *.delete for:
     * companies, employees, complexes, developers.
     *
     * Roles that had *.manage get all 3 new permissions.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $entities = [
            'companies' => 'компаний',
            'employees' => 'сотрудников',
            'complexes' => 'комплексов',
            'developers' => 'девелоперов',
        ];

        foreach ($entities as $entity => $label) {
            // Create new granular permissions
            foreach (['create' => 'Создание', 'edit' => 'Редактирование', 'delete' => 'Удаление'] as $action => $actionLabel) {
                Permission::firstOrCreate(
                    ['name' => "{$entity}.{$action}", 'guard_name' => 'web'],
                    [
                        'group' => $entity,
                        'display_name' => "{$actionLabel} {$label}",
                    ]
                );
            }

            // Transfer *.manage to new permissions for all roles that had it
            $managePermission = Permission::where('name', "{$entity}.manage")->first();
            if ($managePermission) {
                $rolesWithManage = Role::permission("{$entity}.manage")->get();
                foreach ($rolesWithManage as $role) {
                    $role->givePermissionTo([
                        "{$entity}.create",
                        "{$entity}.edit",
                        "{$entity}.delete",
                    ]);
                }

                // Remove old manage permission
                $managePermission->delete();
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse: merge *.create, *.edit, *.delete back into *.manage.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $entities = [
            'companies' => 'Управление компаниями',
            'employees' => 'Управление сотрудниками',
            'complexes' => 'Управление комплексами',
            'developers' => 'Управление девелоперами',
        ];

        foreach ($entities as $entity => $label) {
            $managePermission = Permission::firstOrCreate(
                ['name' => "{$entity}.manage", 'guard_name' => 'web'],
                [
                    'group' => $entity,
                    'display_name' => $label,
                ]
            );

            // Any role that had create, edit, or delete gets manage back
            foreach (['create', 'edit', 'delete'] as $action) {
                $perm = Permission::where('name', "{$entity}.{$action}")->first();
                if ($perm) {
                    $rolesWithPerm = Role::permission("{$entity}.{$action}")->get();
                    foreach ($rolesWithPerm as $role) {
                        if (!$role->hasPermissionTo($managePermission)) {
                            $role->givePermissionTo($managePermission);
                        }
                    }
                    $perm->delete();
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
