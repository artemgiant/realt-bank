<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Get permissions matrix data (roles x permissions).
     */
    public function matrix(): JsonResponse
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        $permissions = Permission::orderBy('group')->orderBy('id')->get();

        // Build matrix: permission_name => [role_id => true/false]
        $matrix = [];
        foreach ($permissions as $permission) {
            $matrix[$permission->name] = [];
            foreach ($roles as $role) {
                $matrix[$permission->name][$role->id] = $role->hasPermissionTo($permission->name);
            }
        }

        return response()->json([
            'roles' => $roles->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'display_name' => $r->display_name ?? $r->name,
            ]),
            'permissions' => $permissions->groupBy('group')->map(fn($perms, $group) => $perms->map(fn($p) => [
                'name' => $p->name,
                'display_name' => $p->display_name ?? $p->name,
                'group' => $p->group,
            ])),
            'matrix' => $matrix,
        ]);
    }

    /**
     * Update permissions for a single role.
     */
    public function updateRole(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions']);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Права роли обновлены',
        ]);
    }

    /**
     * Toggle single permission for a role.
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'required|string|exists:permissions,name',
            'value' => 'required|boolean',
        ]);

        $role = Role::findById($validated['role_id']);

        if ($validated['value']) {
            $role->givePermissionTo($validated['permission']);
        } else {
            $role->revokePermissionTo($validated['permission']);
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => $validated['value'] ? 'Право добавлено' : 'Право удалено',
        ]);
    }

    /**
     * Bulk update permissions matrix.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'matrix' => 'required|array',
            'matrix.*' => 'array',
            'matrix.*.*' => 'boolean',
        ]);

        $roles = Role::all()->keyBy('id');
        $permissions = Permission::all()->keyBy('name');

        foreach ($validated['matrix'] as $permissionName => $roleValues) {
            if (!isset($permissions[$permissionName])) {
                continue;
            }

            foreach ($roleValues as $roleId => $hasPermission) {
                if (!isset($roles[$roleId])) {
                    continue;
                }

                $role = $roles[$roleId];

                if ($hasPermission) {
                    if (!$role->hasPermissionTo($permissionName)) {
                        $role->givePermissionTo($permissionName);
                    }
                } else {
                    if ($role->hasPermissionTo($permissionName)) {
                        $role->revokePermissionTo($permissionName);
                    }
                }
            }
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Матрица прав обновлена',
        ]);
    }
}
