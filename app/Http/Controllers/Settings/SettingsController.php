<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    /**
     * Get common data for all settings pages.
     */
    private function getSettingsData(?string $activeSection = null): array
    {
        $users = User::with(['roles', 'employee.office'])->get();
        $roles = Role::withCount('users')->with('permissions')->get();
        $employees = Employee::active()->orderBy('last_name')->get();
        $permissions = Permission::orderBy('group')->orderBy('id')->get();

        // Build permissions matrix
        $permissionsMatrix = [];
        foreach ($permissions as $permission) {
            $permissionsMatrix[$permission->name] = [];
            foreach ($roles as $role) {
                $permissionsMatrix[$permission->name][$role->id] = $role->hasPermissionTo($permission->name);
            }
        }

        return [
            'users' => $users,
            'roles' => $roles,
            'employees' => $employees,
            'permissions' => $permissions,
            'permissionsMatrix' => $permissionsMatrix,
            'permissionGroups' => $permissions->groupBy('group'),
            'usersCount' => $users->count(),
            'rolesCount' => $roles->count(),
            'activeSection' => $activeSection,
        ];
    }

    /**
     * Display settings page (no section selected - only nav).
     */
    public function index(): View
    {
        return view('pages.settings.index', $this->getSettingsData(null));
    }

    /**
     * Display settings page with users section active.
     */
    public function users(): View
    {
        return view('pages.settings.index', $this->getSettingsData('users'));
    }

    /**
     * Display settings page with roles section active.
     */
    public function roles(): View
    {
        return view('pages.settings.index', $this->getSettingsData('roles'));
    }

    /**
     * Display settings page with permissions section active.
     */
    public function permissions(): View
    {
        return view('pages.settings.index', $this->getSettingsData('permissions'));
    }
}
