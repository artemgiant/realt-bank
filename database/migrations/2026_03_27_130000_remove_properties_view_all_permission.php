<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'properties.view_all')->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(
            ['name' => 'properties.view_all', 'guard_name' => 'web'],
            ['group' => 'properties', 'display_name' => 'Просмотр чужих объектов']
        );

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
