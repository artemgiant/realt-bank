<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(
            ['name' => 'properties.reassign', 'guard_name' => 'web'],
            [
                'group' => 'properties',
                'display_name' => 'Смена агента у объекта',
            ]
        );

        // Назначаем руководящим ролям + team_manager
        $roles = ['agency_director', 'agency_admin', 'office_director', 'team_manager'];
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo('properties.reassign');
            }
        }

        // super_admin получает все permissions
        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::where('name', 'properties.reassign')->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
