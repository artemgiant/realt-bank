<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::with(['roles', 'employee.office'])->get();
        $roles = Role::all();

        return view('pages.settings.users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'employee_id' => $validated['employee_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        // Assign role
        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Пользователь успешно создан');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_id' => $validated['employee_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Sync role
        $role = Role::findById($validated['role_id']);
        $user->syncRoles([$role]);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Пользователь успешно обновлён');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse|JsonResponse
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить самого себя',
                ], 422);
            }

            return redirect()
                ->route('settings.users.index')
                ->with('error', 'Нельзя удалить самого себя');
        }

        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно удалён',
            ]);
        }

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Пользователь успешно удалён');
    }

    /**
     * Get users data for AJAX requests.
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $users = User::with(['roles', 'employee.office'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->get();

        return response()->json([
            'data' => $users,
        ]);
    }
}
