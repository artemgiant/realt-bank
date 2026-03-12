<?php

namespace App\Http\Controllers\Property\Complex;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Property\Complex\Actions\CreateComplex;
use App\Http\Controllers\Property\Complex\Actions\DeleteComplex;
use App\Http\Controllers\Property\Complex\Actions\UpdateComplex;
use App\Http\Controllers\Property\Complex\Presenters\ComplexTablePresenter;
use App\Http\Controllers\Property\Complex\Queries\ComplexIndexQuery;
use App\Http\Controllers\Property\Complex\Requests\StoreComplexRequest;
use App\Http\Controllers\Property\Complex\Requests\UpdateComplexRequest;
use App\Http\Controllers\Property\Complex\ViewData\ComplexFormData;
use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use App\Models\Reference\Developer;
use App\Models\Reference\Dictionary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер комплексов — тонкий координатор.
 *
 * НЕ содержит бизнес-логику, НЕ строит запросы, НЕ форматирует данные.
 * Делегирует работу: Requests, Actions, Queries, Presenters, ViewData.
 */
class ComplexController extends Controller
{
    /**
     * Список комплексов — страница с фильтрами.
     * Сами комплексы загружаются через AJAX (ajaxData).
     */
    public function index(Request $request): View
    {
        return view('pages.complexes.index', [
            'complexCategories' => Dictionary::getComplexCategories(),
            'objectTypes' => Dictionary::getPropertyTypes(),
            'developers' => Developer::orderBy('name')->get(),
            'housingClasses' => Dictionary::getHousingClasses(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'wallTypes' => Dictionary::getWallTypes(),
            'yearsBuilt' => Dictionary::getYearsBuilt(),
            'conditions' => Dictionary::getConditions(),
            'features' => Dictionary::getComplexFeatures(),
        ]);
    }

    /**
     * AJAX endpoint для DataTables Server-Side.
     * Делегирует фильтрацию → ComplexIndexQuery, форматирование → ComplexTablePresenter.
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $sortField = $request->get('sort_field', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $query = new ComplexIndexQuery();
        $query->applyFilters($request)
              ->applySorting($sortField, $sortDir);

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);

        $presenter = new ComplexTablePresenter();

        return response()->json([
            'draw' => (int) $request->get('draw', 1),
            'recordsTotal' => $query->getTotal(),
            'recordsFiltered' => $query->getFiltered(),
            'data' => $presenter->toCollection($query->paginate($start, $length)),
        ]);
    }

    /**
     * Форма создания комплекса. Справочники из ComplexFormData.
     */
    public function create(): View
    {
        return view('pages.complexes.create', ComplexFormData::get());
    }

    /**
     * Создание комплекса. Валидация → StoreComplexRequest, логика → CreateComplex.
     */
    public function store(StoreComplexRequest $request, CreateComplex $action): RedirectResponse
    {
        try {
            $action->execute($request->validated(), $request);

            return redirect()
                ->route('complexes.index')
                ->with('success', 'Комплекс успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании комплекса: ' . $e->getMessage());
        }
    }

    /**
     * Просмотр комплекса.
     */
    public function show(Complex $complex): View
    {
        $complex->load(['developer', 'contacts.phones', 'blocks.street', 'city', 'district', 'zone']);

        return view('pages.complexes.show', [
            'complex' => $complex,
        ]);
    }

    /**
     * Форма редактирования комплекса. Подгружает связи + справочники из ComplexFormData.
     */
    public function edit(Complex $complex): View
    {
        $complex->load([
            'contacts.phones',
            'blocks.street',
            'blocks.heatingType',
            'blocks.wallType',
            'developer',
            'city.state.country',
            'district',
            'zone',
        ]);

        return view('pages.complexes.edit', [
            'complex' => $complex,
            ...ComplexFormData::get(),
        ]);
    }

    /**
     * Обновление комплекса. Валидация → UpdateComplexRequest, логика → UpdateComplex.
     */
    public function update(UpdateComplexRequest $request, Complex $complex, UpdateComplex $action): RedirectResponse
    {
        try {
            $action->execute($complex, $request->validated(), $request);

            return redirect()
                ->route('complexes.index')
                ->with('success', 'Комплекс успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении комплекса: ' . $e->getMessage());
        }
    }

    /**
     * Удаление комплекса. Логика удаления файлов → DeleteComplex.
     */
    public function destroy(Complex $complex, DeleteComplex $action): RedirectResponse
    {
        try {
            $action->execute($complex);

            return redirect()
                ->route('complexes.index')
                ->with('success', 'Комплекс успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении комплекса: ' . $e->getMessage());
        }
    }

    // ========== AJAX методы для Select2 ==========

    /**
     * Поиск комплексов для Select2 AJAX.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 20;

        $query = Complex::with('developer')->active();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $total = $query->count();
        $complexes = $query->orderBy('name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $results = $complexes->map(fn($complex) => [
            'id' => $complex->id,
            'text' => $complex->name,
            'developer_name' => $complex->developer?->name ?? '-',
        ]);

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => ($page * $perPage) < $total],
        ]);
    }

    /**
     * Поиск блоков для Select2 AJAX (по complex_id).
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

        $query = Block::with(['street.zone', 'street.district', 'street.city.region', 'street.city.state'])
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
            $street = $block->street;
            return [
                'id' => $block->id,
                'text' => $block->name,
                'street_id' => $block->street_id,
                'street_name' => $street?->name ?? '',
                'building_number' => $block->building_number ?? '',
                'zone_id' => $street?->zone_id,
                'zone_name' => $street?->zone?->name ?? '',
                'district_id' => $street?->district_id,
                'district_name' => $street?->district?->name ?? '',
                'city_id' => $street?->city_id,
                'city_name' => $street?->city?->name ?? '',
                'region_id' => $street?->city?->region_id,
                'region_name' => $street?->city?->region?->name ?? '',
                'state_id' => $street?->city?->state_id,
                'state_name' => $street?->city?->state?->name ?? '',
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => ($page * $perPage) < $total],
        ]);
    }
}
