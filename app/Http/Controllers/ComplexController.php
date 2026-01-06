<?php

namespace App\Http\Controllers;

use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplexController extends Controller
{
    /**
     * Поиск комплексов для Select2 AJAX
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 20;

        $query = Complex::with('developer')
            ->active();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $total = $query->count();
        $complexes = $query->orderBy('name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $results = $complexes->map(function ($complex) {
            return [
                'id' => $complex->id,
                'text' => $complex->name,
                'developer_name' => $complex->developer?->name ?? '-',
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    /**
     * Поиск блоков для Select2 AJAX (по complex_id)
     */
    public function searchBlocks(Request $request): JsonResponse
    {
        $complexId = $request->get('complex_id');
        $search = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 20;

        if (!$complexId) {
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
            ]);
        }

        $query = Block::with('street')
            ->where('complex_id', $complexId)
            ->active();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $total = $query->count();
        $blocks = $query->orderBy('name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $results = $blocks->map(function ($block) {
            return [
                'id' => $block->id,
                'text' => $block->name,
                'street_id' => $block->street_id,
                'street_name' => $block->street?->name ?? '',
                'building_number' => $block->building_number ?? '',
                'zone_id' => $block->street?->zone_id,
                'zone_name' => $block->street?->zone?->name ?? '',
                'district_id' => $block->street?->district_id,
                'district_name' => $block->street?->district?->name ?? '',
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }
}
