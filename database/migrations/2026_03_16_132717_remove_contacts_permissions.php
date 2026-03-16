<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private array $contactsPermissions = [
        'contacts.view',
        'contacts.create',
        'contacts.edit',
        'contacts.delete',
        'contacts.view_all',
    ];

    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Удаляем все contacts permissions (каскад удалит из role_has_permissions)
        Permission::whereIn('name', $this->contactsPermissions)->delete();

        // Пересинхронизируем super_admin со всеми оставшимися permissions
        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->contactsPermissions as $name) {
            $displayNames = [
                'contacts.view' => 'Просмотр клиентов',
                'contacts.create' => 'Создание клиентов',
                'contacts.edit' => 'Редактирование клиентов',
                'contacts.delete' => 'Удаление клиентов',
                'contacts.view_all' => 'Просмотр чужих клиентов',
            ];
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['group' => 'contacts', 'display_name' => $displayNames[$name]]
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
