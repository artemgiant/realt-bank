<?php

namespace App\Http\Controllers\Property\Property;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Property\Property\Actions\CreateProperty;
use App\Http\Controllers\Property\Property\Actions\UpdateProperty;
use App\Http\Controllers\Property\Property\Presenters\PropertyTablePresenter;
use App\Http\Controllers\Property\Property\Queries\PropertyIndexQuery;
use App\Http\Controllers\Property\Property\Requests\StorePropertyRequest;
use App\Http\Controllers\Property\Property\Requests\UpdatePropertyRequest;
use App\Http\Controllers\Property\Property\ViewData\PropertyFormData;
use App\Models\Employee\Employee;
use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use App\Models\Reference\Currency;
use App\Models\Reference\Developer;
use App\Models\Reference\Dictionary;
use App\Services\PhotoUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер объектов недвижимости — тонкий координатор.
 *
 * НЕ содержит бизнес-логику, НЕ строит запросы, НЕ форматирует данные.
 * Делегирует работу: Requests, Actions, Queries, Presenters, ViewData.
 */
class PropertyController extends Controller
{
    /**
     * Список объектов — страница с фильтрами.
     * Сами объекты загружаются через AJAX (ajaxData).
     */
    public function index(Request $request): View
    {
        return view('pages.properties.index', [
            'dealTypes' => Dictionary::getDealTypes(),
            'dealKinds' => Dictionary::getDealKinds(),
            'propertyTypes' => Dictionary::getPropertyTypes(),
            'conditions' => Dictionary::getConditions(),
            'buildingTypes' => Dictionary::getBuildingTypes(),
            'wallTypes' => Dictionary::getWallTypes(),
            'roomCounts' => Dictionary::getRoomCounts(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'bathroomCounts' => Dictionary::getBathroomCounts(),
            'ceilingHeights' => Dictionary::getCeilingHeights(),
            'features' => Dictionary::getFeatures(),
            'currencies' => Currency::active()->get(),
            'developers' => Developer::active()->orderBy('name')->get(),
            'yearsBuilt' => Dictionary::getYearsBuilt(),
            'propertyStatuses' => Dictionary::getPropertyStatuses(),
            'filters' => $request->only([
                'deal_type_id', 'price_from', 'price_to', 'currency_id',
                'area_from', 'area_to', 'area_living_from', 'area_living_to',
                'area_kitchen_from', 'area_kitchen_to', 'area_land_from', 'area_land_to',
                'floor_from', 'floor_to', 'floors_total_from', 'floors_total_to',
                'price_per_m2_from', 'price_per_m2_to',
                'room_count_id', 'property_type_id', 'condition_id', 'building_type_id',
                'year_built', 'wall_type_id', 'heating_type_id', 'bathroom_count_id',
                'ceiling_height_id', 'features', 'developer_id',
                'status', 'search_id', 'contact_search', 'created_from', 'created_to',
            ]),
        ]);
    }

    /**
     * AJAX endpoint для DataTables Server-Side.
     * Делегирует фильтрацию → PropertyIndexQuery, форматирование → PropertyTablePresenter.
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 20);
        $searchValue = $request->input('search.value', '');
        $sortField = $request->input('sort_field', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $targetCurrency = $request->filled('currency_id')
            ? Currency::find($request->currency_id)
            : null;

        $query = new PropertyIndexQuery();
        $query->applyFilters($request, $targetCurrency)
              ->applySearch($searchValue)
              ->applySorting($sortField, $sortDir);

        $presenter = new PropertyTablePresenter();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $query->getTotal(),
            'recordsFiltered' => $query->getFiltered(),
            'data' => $presenter->toCollection($query->paginate($start, $length), $targetCurrency),
        ]);
    }

    /**
     * Форма создания объекта. Справочники из PropertyFormData.
     */
    public function create(): View
    {
        $agent = Employee::where('user_id', auth()->id())->first();

        return view('pages.properties.create', [
            'agent' => $agent,
            ...PropertyFormData::get(),
        ]);
    }

    /**
     * Создание объекта. Валидация → StorePropertyRequest, логика → CreateProperty.
     */
    public function store(StorePropertyRequest $request, CreateProperty $action): RedirectResponse
    {
        try {
            $action->execute($request->validated(), $request);

            return redirect()
                ->route('properties.index')
                ->with('success', 'Объект успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании объекта: ' . $e->getMessage());
        }
    }

    /**
     * Форма редактирования объекта. Подгружает связи + справочники из PropertyFormData.
     */
    public function edit(Property $property): View
    {
        $property->load([
            'contacts.phones', 'contacts.roles', 'translations', 'features',
            'photos', 'documents', 'employee.company',
            'complex', 'block', 'country', 'state', 'city', 'district', 'zone', 'street',
        ]);

        $agent = Employee::where('user_id', auth()->id())->first();

        $backUrl = url()->previous();

        return view('pages.properties.edit', [
            'property' => $property,
            'agent' => $agent,
            'backUrl' => $backUrl,
            ...PropertyFormData::get(),
        ]);
    }

    /**
     * Обновление объекта. Валидация → UpdatePropertyRequest, логика → UpdateProperty.
     */
    public function update(UpdatePropertyRequest $request, Property $property, UpdateProperty $action): RedirectResponse
    {
        try {
            $action->execute($property, $request->validated(), $request);

            $redirectTo = $request->input('redirect_to');

            if ($redirectTo && str_starts_with($redirectTo, url('/'))) {
                return redirect($redirectTo)
                    ->with('success', 'Объект успешно обновлён!');
            }

            return redirect()
                ->route('properties.index')
                ->with('success', 'Объект успешно обновлён!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении объекта: ' . $e->getMessage());
        }
    }

    /**
     * Удаление объекта (soft delete).
     */
    public function destroy(Property $property): RedirectResponse
    {
        $property->delete();

        return redirect()
            ->route('properties.index')
            ->with('success', 'Объект #' . $property->id . ' успешно удалён.');
    }

    /**
     * AJAX: Удалить фото объекта. Проверяет принадлежность фото к объекту.
     */
    public function deletePhoto(Property $property, PropertyPhoto $photo): JsonResponse
    {
        if ($photo->property_id !== $property->id) {
            return response()->json(['success' => false, 'message' => 'Фото не принадлежит этому объекту'], 403);
        }

        $photoService = app(PhotoUploadService::class);
        $result = $photoService->deletePhoto($photo);

        return response()->json(['success' => $result]);
    }

    /**
     * AJAX: Проверка дублирования адреса объекта.
     */
    public function checkDuplicateAddress(Request $request): JsonResponse
    {
        $streetId = $request->input('street_id');
        $buildingNumber = $request->input('building_number');

        if (!$streetId || !$buildingNumber) {
            return response()->json(['exists' => false]);
        }

        $query = Property::where('street_id', $streetId)
            ->where('building_number', $buildingNumber);

        // Определяем по типу сделки, нужен ли номер квартиры
        $dealTypeId = $request->input('deal_type_id');
        $apartmentNumber = $request->input('apartment_number');

        if ($dealTypeId) {
            // Ищем дубликаты только среди объектов с таким же типом сделки
            $query->where('deal_type_id', $dealTypeId);

            $dealType = \App\Models\Reference\Dictionary::find($dealTypeId);
            $dealTypeName = $dealType ? $dealType->name : '';
            $hasApartment = str_contains(mb_strtolower($dealTypeName), 'комнат')
                || str_contains(mb_strtolower($dealTypeName), 'квартир');

            if ($hasApartment && $apartmentNumber) {
                // Тип сделки с квартирой — проверяем точное совпадение номера квартиры
                $query->where('apartment_number', $apartmentNumber);
            }
            // Тип без квартиры — ищем только по улице + дому
        } else {
            // Тип сделки не выбран — проверяем по номеру квартиры если указан
            if ($apartmentNumber) {
                $query->where('apartment_number', $apartmentNumber);
            }
        }

        // Исключаем текущий объект при редактировании
        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', $request->input('exclude_id'));
        }

        $duplicates = $query->with(['street', 'city', 'employee'])
            ->limit(5)
            ->get();

        if ($duplicates->isEmpty()) {
            return response()->json(['exists' => false]);
        }

        $items = $duplicates->map(function (Property $p) {
            return [
                'id' => $p->id,
                'address' => $p->full_address,
                'apartment_number' => $p->apartment_number,
                'price' => $p->price ? number_format($p->price, 0, '.', ' ') : null,
                'currency' => $p->currency?->code,
                'agent' => $p->employee?->full_name,
                'status' => $p->status,
                'edit_url' => route('properties.edit', $p->id),
            ];
        });

        return response()->json([
            'exists' => true,
            'duplicates' => $items,
        ]);
    }

    /**
     * AJAX: Обновить дату актуальности объекта (updated_at = now).
     */
    public function refreshUpdatedAt(Property $property): JsonResponse
    {
        $property->touch();

        return response()->json([
            'success' => true,
            'updated_at_formatted' => $property->updated_at->format('d.m.Y'),
        ]);
    }

    /**
     * AJAX: Изменить порядок фотографий объекта (drag & drop).
     */
    public function reorderPhotos(Request $request, Property $property): JsonResponse
    {
        $photoIds = $request->input('photo_ids', []);

        if (empty($photoIds)) {
            return response()->json(['success' => false, 'message' => 'Нет фото для сортировки'], 400);
        }

        $photoService = app(PhotoUploadService::class);
        $result = $photoService->updateOrder($property, $photoIds);

        return response()->json(['success' => $result]);
    }
}
