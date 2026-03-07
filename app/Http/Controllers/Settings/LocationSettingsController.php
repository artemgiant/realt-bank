<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\Location\State;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocationSettingsController extends Controller
{
    // ========== AJAX helpers (for cascading Select2) ==========

    public function getRegions(Request $request): JsonResponse
    {
        $regions = Region::query()
            ->when($request->state_id, fn ($q, $id) => $q->where('state_id', $id))
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'state_id']);

        return response()->json(['results' => $regions]);
    }

    public function getCities(Request $request): JsonResponse
    {
        $cities = City::query()
            ->when($request->state_id, fn ($q, $id) => $q->where('state_id', $id))
            ->when($request->region_id, fn ($q, $id) => $q->where('region_id', $id))
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'state_id', 'region_id']);

        return response()->json(['results' => $cities]);
    }

    public function getDistricts(Request $request): JsonResponse
    {
        $districts = District::query()
            ->when($request->city_id, fn ($q, $id) => $q->where('city_id', $id))
            ->orderBy('name')
            ->get(['id', 'name', 'city_id']);

        return response()->json(['results' => $districts]);
    }

    public function getZones(Request $request): JsonResponse
    {
        $zones = Zone::query()
            ->when($request->city_id, fn ($q, $id) => $q->where('city_id', $id))
            ->when($request->district_id, fn ($q, $id) => $q->where('district_id', $id))
            ->orderBy('name')
            ->get(['id', 'name', 'city_id', 'district_id']);

        return response()->json(['results' => $zones]);
    }

    // ========== Countries CRUD ==========

    public function storeCountry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
        ]);

        Country::create($validated);

        return redirect()->route('settings.countries.index')->with('success', 'Страна добавлена');
    }

    public function updateCountry(Request $request, Country $country): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
        ]);

        $country->update($validated);

        return redirect()->route('settings.countries.index')->with('success', 'Страна обновлена');
    }

    public function destroyCountry(Country $country): RedirectResponse
    {
        $statesCount = $country->states()->count();
        if ($statesCount > 0) {
            return redirect()->route('settings.countries.index')
                ->with('error', "Невозможно удалить: в стране есть {$statesCount} регион(ов)");
        }

        $country->delete();

        return redirect()->route('settings.countries.index')->with('success', 'Страна удалена');
    }

    // ========== States CRUD ==========

    public function storeState(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        State::create($validated);

        return redirect()->route('settings.regions.index')->with('success', 'Регион добавлен');
    }

    public function updateState(Request $request, State $state): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $state->update($validated);

        return redirect()->route('settings.regions.index')->with('success', 'Регион обновлён');
    }

    public function destroyState(State $state): RedirectResponse
    {
        $citiesCount = $state->cities()->count();
        if ($citiesCount > 0) {
            return redirect()->route('settings.regions.index')
                ->with('error', "Невозможно удалить: в регионе есть {$citiesCount} город(ов)");
        }

        $state->delete();

        return redirect()->route('settings.regions.index')->with('success', 'Регион удалён');
    }

    // ========== Regions (Районы области) CRUD ==========

    public function storeRegion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        Region::create($validated);

        return redirect()->route('settings.regions.index')->with('success', 'Район области добавлен');
    }

    public function updateRegion(Request $request, Region $region): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $region->update($validated);

        return redirect()->route('settings.regions.index')->with('success', 'Район области обновлён');
    }

    public function destroyRegion(Region $region): RedirectResponse
    {
        $citiesCount = $region->cities()->count();
        if ($citiesCount > 0) {
            return redirect()->route('settings.regions.index')
                ->with('error', "Невозможно удалить: в районе области есть {$citiesCount} город(ов)");
        }

        $region->delete();

        return redirect()->route('settings.regions.index')->with('success', 'Район области удалён');
    }

    // ========== Districts CRUD ==========

    public function storeDistrict(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        District::create($validated);

        return redirect()->route('settings.regions.index')->with('success', 'Район добавлен');
    }

    public function updateDistrict(Request $request, District $district): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        $district->update($validated);

        return redirect()->route('settings.regions.index')->with('success', 'Район обновлён');
    }

    public function destroyDistrict(District $district): RedirectResponse
    {
        $streetsCount = $district->streets()->count();
        if ($streetsCount > 0) {
            return redirect()->route('settings.regions.index')
                ->with('error', "Невозможно удалить: в районе есть {$streetsCount} улиц(а)");
        }

        $district->delete();

        return redirect()->route('settings.regions.index')->with('success', 'Район удалён');
    }

    // ========== Cities CRUD ==========

    public function storeCity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'region_id' => 'nullable|exists:regions,id',
        ]);

        $validated['type'] = 'city';

        City::create($validated);

        return redirect()->route('settings.cities.index')->with('success', 'Город добавлен');
    }

    public function updateCity(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'region_id' => 'nullable|exists:regions,id',
        ]);

        $city->update($validated);

        return redirect()->route('settings.cities.index')->with('success', 'Город обновлён');
    }

    public function destroyCity(City $city): RedirectResponse
    {
        $streetsCount = $city->streets()->count();
        if ($streetsCount > 0) {
            return redirect()->route('settings.cities.index')
                ->with('error', "Невозможно удалить: в городе есть {$streetsCount} улиц(а)");
        }

        $city->delete();

        return redirect()->route('settings.cities.index')->with('success', 'Город удалён');
    }

    // ========== Zones CRUD ==========

    public function storeZone(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
        ]);

        $city = \App\Models\Location\City::findOrFail($validated['city_id']);
        $validated['state_id'] = $city->state_id;

        Zone::create($validated);

        return redirect()->route('settings.zones.index')->with('success', 'Микрорайон добавлен');
    }

    public function updateZone(Request $request, Zone $zone): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
        ]);

        $city = \App\Models\Location\City::findOrFail($validated['city_id']);
        $validated['state_id'] = $city->state_id;

        $zone->update($validated);

        return redirect()->route('settings.zones.index')->with('success', 'Микрорайон обновлён');
    }

    public function destroyZone(Zone $zone): RedirectResponse
    {
        $streetsCount = $zone->streets()->count();
        if ($streetsCount > 0) {
            return redirect()->route('settings.zones.index')
                ->with('error', "Невозможно удалить: в микрорайоне есть {$streetsCount} улиц(а)");
        }

        $zone->delete();

        return redirect()->route('settings.zones.index')->with('success', 'Микрорайон удалён');
    }

    // ========== Streets CRUD ==========

    public function storeStreet(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'zone_id' => 'nullable|exists:zones,id',
        ]);

        Street::create($validated);

        return redirect()->route('settings.streets.index')->with('success', 'Улица добавлена');
    }

    public function updateStreet(Request $request, Street $street): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'zone_id' => 'nullable|exists:zones,id',
        ]);

        $street->update($validated);

        return redirect()->route('settings.streets.index')->with('success', 'Улица обновлена');
    }

    public function destroyStreet(Street $street): RedirectResponse
    {
        $street->delete();

        return redirect()->route('settings.streets.index')->with('success', 'Улица удалена');
    }
}
