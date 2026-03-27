<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        $perms = [
            'properties.reassign_office' => 'Смена агента (офис)',
            'properties.reassign_company' => 'Смена агента (компания)',
        ];

        foreach ($perms as $name => $displayName) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['group' => 'properties', 'display_name' => $displayName]
            );
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::whereIn('name', [
            'properties.reassign_office',
            'properties.reassign_company',
        ])->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
