<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use App\Models\User;
use Illuminate\Http\Request;
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
            // Location counts (always available for nav badges)
            'countriesCount' => Country::count(),
            'statesCount' => State::count(),
            'citiesCount' => City::count(),
            'districtsCount' => District::count(),
            'zonesCount' => Zone::count(),
            'streetsCount' => Street::count(),
        ];
    }

    /**
     * Get per_page value from request (clamped to allowed values).
     */
    private function getPerPage(Request $request, int $default = 10): int
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->input('per_page', $default);

        return in_array($perPage, $allowed) ? $perPage : $default;
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

    /**
     * Display settings page with countries section active.
     */
    public function countries(Request $request): View
    {
        $data = $this->getSettingsData('countries');
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Country::withCount('states')->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $data['countriesList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['search'] = $search;
        $data['perPage'] = $perPage;

        return view('pages.settings.index', $data);
    }

    /**
     * Display settings page with regions (states + districts) section active.
     */
    public function regions(Request $request): View
    {
        $data = $this->getSettingsData('regions');
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = State::withCount(['cities', 'districts'])
            ->with(['country', 'cities' => function ($q) {
                $q->withCount('districts')->orderBy('name');
            }, 'cities.districts' => function ($q) {
                $q->withCount('streets')->orderBy('name');
            }])
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $data['states'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['search'] = $search;
        $data['perPage'] = $perPage;

        // Needed for district drawer (city selector) — named differently to avoid collision with paginated $citiesList
        $data['allCities'] = City::with('state')->orderBy('name')->get();

        // Needed for state drawer (country selector)
        $data['countriesForState'] = Country::orderBy('name')->get();

        return view('pages.settings.index', $data);
    }

    /**
     * Display settings page with cities section active.
     */
    public function cities(Request $request): View
    {
        $data = $this->getSettingsData('cities');
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = City::with('state')
            ->withCount(['streets', 'districts', 'zones'])
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('state', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $data['citiesList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['statesList'] = State::active()->with('country')->orderBy('name')->get();
        $data['search'] = $search;
        $data['perPage'] = $perPage;

        return view('pages.settings.index', $data);
    }

    /**
     * Display settings page with zones section active.
     */
    public function zones(Request $request): View
    {
        $data = $this->getSettingsData('zones');
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Zone::with(['city.state', 'district'])
            ->withCount('streets')
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('city', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('district', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $data['zonesList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['statesList'] = State::active()->orderBy('name')->get();
        $data['zoneCities'] = City::with('state.country')->orderBy('name')->get();
        $data['search'] = $search;
        $data['perPage'] = $perPage;

        return view('pages.settings.index', $data);
    }

    /**
     * Display settings page with streets section active.
     */
    public function streets(Request $request): View
    {
        $data = $this->getSettingsData('streets');
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Street::with(['city.state.country', 'district', 'zone'])
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('city', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('district', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('zone', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $data['streetsList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['statesList'] = State::active()->orderBy('name')->get();
        $data['streetCities'] = City::with('state.country')->orderBy('name')->get();
        $data['search'] = $search;
        $data['perPage'] = $perPage;

        return view('pages.settings.index', $data);
    }
}
