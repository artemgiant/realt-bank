<?php

namespace App\Http\Controllers;

use App\Models\Location\LibStreet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Поиск улиц по названию
     * GET /location/search?q=поисковый_запрос
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('q', '');
        $limit = $request->input('limit', 15);

        // Минимум 2 символа для поиска
        if (mb_strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'results' => [],
                'message' => 'Введите минимум 2 символа',
            ]);
        }

        $streets = LibStreet::with(['zone', 'region', 'town'])
            ->active()
            ->search($search)
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $results = $streets->map(function ($street) {
            return [
                'id' => $street->id,
                'name' => $street->name,
                'zone_id' => $street->zone_id,
                'zone_name' => $street->zone?->name,
                'region_id' => $street->region_id,
                'region_name' => $street->region?->name,
                'town_id' => $street->town_id,
                'town_name' => $street->town?->name,
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
        $street = LibStreet::with(['zone', 'region', 'town'])
            ->active()
            ->find($id);

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
                'zone_id' => $street->zone_id,
                'zone_name' => $street->zone?->name,
                'region_id' => $street->region_id,
                'region_name' => $street->region?->name,
                'town_id' => $street->town_id,
                'town_name' => $street->town?->name,
                'full_address' => $street->full_address,
                'short_address' => $street->short_address,
            ],
        ]);
    }
}
