<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Удаляем archive и restore
        Permission::where('name', 'properties.archive')->delete();
        Permission::where('name', 'properties.restore')->delete();

        // Добавляем новые права
        Permission::firstOrCreate(
            ['name' => 'properties.view_office', 'guard_name' => 'web'],
            ['group' => 'properties', 'display_name' => 'Просмотр объектов своего офиса']
        );

        Permission::firstOrCreate(
            ['name' => 'properties.view_company', 'guard_name' => 'web'],
            ['group' => 'properties', 'display_name' => 'Просмотр объектов своей компании']
        );

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'properties.view_office')->delete();
        Permission::where('name', 'properties.view_company')->delete();

        Permission::firstOrCreate(
            ['name' => 'properties.archive', 'guard_name' => 'web'],
            ['group' => 'properties', 'display_name' => 'Архивирование объектов']
        );

        Permission::firstOrCreate(
            ['name' => 'properties.restore', 'guard_name' => 'web'],
            ['group' => 'properties', 'display_name' => 'Восстановление объектов']
        );

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
