<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
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
            'regionsCount' => Region::count(),
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

        $query = State::withCount(['cities', 'regions', 'districts'])
            ->with(['country', 'regions' => function ($q) {
                $q->withCount('cities')->orderBy('name');
            }, 'cities' => function ($q) {
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

        // Needed for region drawer (state selector)
        $data['statesForRegion'] = State::active()->orderBy('name')->get();

        return view('pages.settings.index', $data);
    }

    /**
     * Display settings page with oblast regions section active.
     */
    public function oblastRegions(Request $request): View
    {
        $data = $this->getSettingsData('oblast-regions');
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Region::with('state')
            ->withCount('cities')
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $data['regionsList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['statesForRegion'] = State::active()->orderBy('name')->get();
        $data['search'] = $search;
        $data['perPage'] = $perPage;

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
            $query->where('name', 'like', "%{$search}%");
        }

        $data['citiesList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['statesList'] = State::active()->with('country')->orderBy('name')->get();
        $data['search'] = $search;
        $data['perPage'] = $perPage;

        return view('pages.settings.index', $data);
    }

    /**
     * AJAX search for cities — returns partial HTML.
     */
    public function citiesAjaxSearch(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = City::with('state')
            ->withCount(['streets', 'districts', 'zones'])
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $citiesList = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));

        $html = view('pages.settings.locations.partials.cities-list', [
            'citiesList' => $citiesList,
            'perPage' => $perPage,
            'search' => $search,
        ])->render();

        return response()->json([
            'html' => $html,
            'total' => $citiesList->total(),
            'data' => $citiesList->keyBy('id'),
        ]);
    }

    /**
     * Display settings page with districts section active.
     */
    public function districts(Request $request): View
    {
        $data = $this->getSettingsData('districts');
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = District::with('city')
            ->withCount('streets')
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $data['districtsList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['allCities'] = City::with('state')->orderBy('name')->get();
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

        $query = Zone::with(['city', 'state'])
            ->withCount('streets')
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $data['zonesList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['allCities'] = City::with('state')->orderBy('name')->get();
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
            $query->where('name', 'like', "%{$search}%");
        }

        $data['streetsList'] = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));
        $data['statesList'] = State::active()->orderBy('name')->get();
        $data['streetCities'] = City::with('state.country')->orderBy('name')->get();
        $data['search'] = $search;
        $data['perPage'] = $perPage;

        return view('pages.settings.index', $data);
    }

    /**
     * AJAX search for countries.
     */
    public function countriesAjaxSearch(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Country::withCount('states')->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $countriesList = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));

        return response()->json([
            'html' => view('pages.settings.locations.partials.countries-list', [
                'countriesList' => $countriesList,
                'perPage' => $perPage,
                'search' => $search,
            ])->render(),
            'total' => $countriesList->total(),
            'data' => $countriesList->keyBy('id'),
        ]);
    }

    /**
     * AJAX search for regions (states).
     */
    public function regionsAjaxSearch(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = State::withCount(['cities', 'regions', 'districts'])
            ->with(['country', 'regions' => function ($q) {
                $q->withCount('cities')->orderBy('name');
            }, 'cities' => function ($q) {
                $q->withCount('districts')->orderBy('name');
            }, 'cities.districts' => function ($q) {
                $q->withCount('streets')->orderBy('name');
            }])
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $states = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));

        return response()->json([
            'html' => view('pages.settings.locations.partials.regions-list', [
                'states' => $states,
                'perPage' => $perPage,
                'search' => $search,
            ])->render(),
            'total' => $states->total(),
            'data' => $states->keyBy('id'),
        ]);
    }

    /**
     * AJAX search for oblast regions.
     */
    public function oblastRegionsAjaxSearch(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Region::with('state')
            ->withCount('cities')
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $regionsList = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));

        return response()->json([
            'html' => view('pages.settings.locations.partials.oblast-regions-list', [
                'regionsList' => $regionsList,
                'perPage' => $perPage,
                'search' => $search,
            ])->render(),
            'total' => $regionsList->total(),
            'data' => $regionsList->keyBy('id'),
        ]);
    }

    /**
     * AJAX search for districts.
     */
    public function districtsAjaxSearch(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = District::with('city')
            ->withCount('streets')
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $districtsList = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));

        return response()->json([
            'html' => view('pages.settings.locations.partials.districts-list', [
                'districtsList' => $districtsList,
                'perPage' => $perPage,
                'search' => $search,
            ])->render(),
            'total' => $districtsList->total(),
            'data' => $districtsList->keyBy('id'),
        ]);
    }

    /**
     * AJAX search for zones.
     */
    public function zonesAjaxSearch(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Zone::with(['city', 'state'])
            ->withCount('streets')
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $zonesList = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));

        return response()->json([
            'html' => view('pages.settings.locations.partials.zones-list', [
                'zonesList' => $zonesList,
                'perPage' => $perPage,
                'search' => $search,
            ])->render(),
            'total' => $zonesList->total(),
            'data' => $zonesList->keyBy('id'),
        ]);
    }

    /**
     * AJAX search for streets.
     */
    public function streetsAjaxSearch(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $this->getPerPage($request);

        $query = Street::with(['city.state.country', 'district', 'zone'])
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $streetsList = $query->paginate($perPage)->appends($request->only(['search', 'per_page']));

        return response()->json([
            'html' => view('pages.settings.locations.partials.streets-list', [
                'streetsList' => $streetsList,
                'perPage' => $perPage,
                'search' => $search,
            ])->render(),
            'total' => $streetsList->total(),
            'data' => $streetsList->keyBy('id'),
        ]);
    }
}
