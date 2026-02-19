<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(): View
    {
        $roles = Role::withCount('users')->get();

        return view('pages.settings.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $roles = Role::all(); // Для выбора "Скопировать права из"

        return view('pages.settings.roles.create', compact('roles'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:admin,manager,agent',
            'copy_from' => 'nullable|exists:roles,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'guard_name' => 'web',
        ]);

        // Копируем права из другой роли если указано
        if (!empty($validated['copy_from'])) {
            $sourceRole = Role::findById($validated['copy_from']);
            $permissions = $sourceRole->permissions->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }

        return redirect()
            ->route('settings.roles.index')
            ->with('success', 'Роль успешно создана');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $roles = Role::where('id', '!=', $role->id)->get();

        return view('pages.settings.roles.edit', compact('role', 'roles'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:admin,manager,agent',
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
        ]);

        return redirect()
            ->route('settings.roles.index')
            ->with('success', 'Роль успешно обновлена');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse|JsonResponse
    {
        // Проверяем, есть ли пользователи с этой ролью
        $usersCount = $role->users()->count();

        if ($usersCount > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Невозможно удалить роль. У неё есть {$usersCount} пользователей.",
                ], 422);
            }

            return redirect()
                ->route('settings.roles.index')
                ->with('error', "Невозможно удалить роль. У неё есть {$usersCount} пользователей.");
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Роль успешно удалена',
            ]);
        }

        return redirect()
            ->route('settings.roles.index')
            ->with('success', 'Роль успешно удалена');
    }

    /**
     * Get roles data for AJAX requests (DataTables, Select2, etc.)
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $roles = Role::withCount('users')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            })
            ->get();

        return response()->json([
            'data' => $roles,
        ]);
    }
}
