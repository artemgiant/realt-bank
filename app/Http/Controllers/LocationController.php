<?php

namespace App\Http\Controllers;

use App\Models\Location\Street;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Поиск улиц для autocomplete
     * GET /location/search?q=поисковый_запрос
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 15);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Минимум 2 символа для поиска',
                'results' => [],
            ]);
        }

        $streets = Street::with(['city', 'district', 'zone'])
            ->active()
            ->search($query)
            ->limit($limit)
            ->get();

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
}
