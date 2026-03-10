<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Location\Presenters\LocationPresenter;
use App\Http\Controllers\Location\Queries\LocationFilterQuery;
use App\Models\Location\Region;
use App\Models\Location\State;
use App\Models\Location\Street;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Контроллер локаций — тонкий координатор.
 *
 * НЕ содержит бизнес-логику, НЕ форматирует данные.
 * Делегирует работу: Queries (построение данных фильтра), Presenters (форматирование).
 * Контроллер только для чтения — без store/update/delete.
 */
class LocationController extends Controller
{
    /**
     * Поиск улиц для autocomplete.
     * GET /location/search?q=поисковый_запрос&state_id=ID_региона
     */
    public function search(Request $request, LocationPresenter $presenter): JsonResponse
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

        $results = $streets->map(fn(Street $street) => $presenter->formatStreet($street));

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $results->count(),
        ]);
    }

    /**
     * Получение данных улицы по ID.
     * GET /location/street/{id}
     */
    public function show(int $id, LocationPresenter $presenter): JsonResponse
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
            'street' => $presenter->formatStreet($street),
        ]);
    }

    /**
     * Поиск регионов для autocomplete.
     * GET /location/states/search?q=поисковый_запрос
     */
    public function searchStates(Request $request, LocationPresenter $presenter): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 15);

        $statesQuery = State::with('country')->active();

        if (strlen($query) >= 1) {
            $statesQuery->where('name', 'like', "%{$query}%");
        }

        $states = $statesQuery->orderBy('name')->limit($limit)->get();

        $results = $states->map(fn(State $state) => $presenter->formatState($state));

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $results->count(),
        ]);
    }

    /**
     * Получение региона по умолчанию (Одесский регион).
     * GET /location/states/default
     */
    public function getDefaultState(LocationPresenter $presenter): JsonResponse
    {
        // Ищем Одесский регион
        $state = State::with('country')
            ->active()
            ->where('name', 'like', '%Одес%')
            ->first();

        if (!$state) {
            // Если не найден — берём первый активный
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
            'state' => $presenter->formatState($state),
        ]);
    }

    /**
     * Получение региона по ID.
     * GET /location/states/{id}
     */
    public function showState(int $id, LocationPresenter $presenter): JsonResponse
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
            'state' => $presenter->formatState($state),
        ]);
    }

    /**
     * Получение данных для фильтра локаций.
     * GET /location/filter-data
     */
    public function getFilterData(Request $request, LocationFilterQuery $filterQuery): JsonResponse
    {
        $data = $filterQuery->execute($request);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Получение районов области по state_id.
     * GET /location/regions/by-state?state_id=ID
     */
    public function getRegionsByState(Request $request): JsonResponse
    {
        $stateId = $request->input('state_id');

        if (!$stateId) {
            return response()->json(['success' => false, 'results' => []]);
        }

        $regions = Region::where('state_id', $stateId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'state_id']);

        return response()->json([
            'success' => true,
            'results' => $regions,
        ]);
    }

    /**
     * Универсальный поиск локации (страна/область/город).
     * GET /location/search-all?q=поисковый_запрос
     */
    public function searchAll(Request $request, LocationPresenter $presenter): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Минимум 2 символа для поиска',
                'results' => [],
            ]);
        }

        $results = $presenter->formatSearchAllResults($request);

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results),
        ]);
    }
}
