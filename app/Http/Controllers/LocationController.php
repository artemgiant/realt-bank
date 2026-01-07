<?php

namespace App\Http\Controllers;

use App\Models\Location\State;
use App\Models\Location\Street;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Поиск улиц для autocomplete
     * GET /location/search?q=поисковый_запрос&state_id=ID_региона
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $stateId = $request->input('state_id');
        $limit = $request->input('limit', 15);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Минимум 2 символа для поиска',
                'results' => [],
            ]);
        }

        $streetsQuery = Street::with(['city', 'district', 'zone'])
            ->active()
            ->search($query);

        // Фильтрация по региону через город
        if ($stateId) {
            $streetsQuery->whereHas('city', function ($q) use ($stateId) {
                $q->where('state_id', $stateId);
            });
        }

        $streets = $streetsQuery->limit($limit)->get();

        $results = $streets->map(function ($street) {
            return [
                'id' => $street->id,
                'name' => $street->name,
                'city_id' => $street->city_id,
                'city_name' => $street->city?->name,
                'district_id' => $street->district_id,
                'district_name' => $street->district?->name,
                'zone_id' => $street->zone_id,
                'zone_name' => $street->zone?->name,
                'full_address' => $street->full_address,
                'short_address' => $street->short_address,
            ];
        });

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $results->count(),
        ]);
    }

    /**
     * Получение данных улицы по ID
     * GET /location/street/{id}
     */
    public function show(int $id): JsonResponse
    {
        $street = Street::with(['city', 'district', 'zone'])->find($id);

        if (!$street) {
            return response()->json([
                'success' => false,
                'message' => 'Улица не найдена',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'street' => [
                'id' => $street->id,
                'name' => $street->name,
                'city_id' => $street->city_id,
                'city_name' => $street->city?->name,
                'district_id' => $street->district_id,
                'district_name' => $street->district?->name,
                'zone_id' => $street->zone_id,
                'zone_name' => $street->zone?->name,
                'full_address' => $street->full_address,
                'short_address' => $street->short_address,
            ],
        ]);
    }

    /**
     * Поиск регионов для autocomplete
     * GET /location/states/search?q=поисковый_запрос
     */
    public function searchStates(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 15);

        $statesQuery = State::with('country')->active();

        if (strlen($query) >= 1) {
            $statesQuery->where('name', 'like', "%{$query}%");
        }

        $states = $statesQuery->orderBy('name')->limit($limit)->get();

        $results = $states->map(function ($state) {
            return [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $state->country_id,
                'country_name' => $state->country?->name,
                'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
            ];
        });

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $results->count(),
        ]);
    }

    /**
     * Получение региона по умолчанию (Одесский регион)
     * GET /location/states/default
     */
    public function getDefaultState(): JsonResponse
    {
        // Ищем Одесский регион
        $state = State::with('country')
            ->active()
            ->where('name', 'like', '%Одес%')
            ->first();

        if (!$state) {
            // Если не найден - берем первый активный
            $state = State::with('country')->active()->first();
        }

        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'Регион не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'state' => [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $state->country_id,
                'country_name' => $state->country?->name,
                'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
            ],
        ]);
    }

    /**
     * Получение региона по ID
     * GET /location/states/{id}
     */
    public function showState(int $id): JsonResponse
    {
        $state = State::with('country')->find($id);

        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'Регион не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'state' => [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $state->country_id,
                'country_name' => $state->country?->name,
                'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
            ],
        ]);
    }
}
