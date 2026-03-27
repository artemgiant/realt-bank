<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'properties.edit_office' => 'Редактирование объектов офиса',
            'properties.edit_company' => 'Редактирование объектов компании',
            'properties.delete_office' => 'Удаление объектов офиса',
            'properties.delete_company' => 'Удаление объектов компании',
        ];

        foreach ($permissions as $name => $displayName) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['group' => 'properties', 'display_name' => $displayName]
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', [
            'properties.edit_office',
            'properties.edit_company',
            'properties.delete_office',
            'properties.delete_company',
        ])->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
