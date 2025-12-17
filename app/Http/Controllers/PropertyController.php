<?php

namespace App\Http\Controllers;

use App\Models\Contact\Contact;
use App\Models\Location\Country;
use App\Models\Property\Property;
use App\Models\Property\PropertyDocument;
use App\Models\Property\PropertyPhoto;
use App\Models\Property\PropertyTranslation;
use App\Models\Reference\Complex;
use App\Models\Reference\Currency;
use App\Models\Reference\Developer;
use App\Models\Reference\Dictionary;
use App\Models\Reference\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display a listing of the properties.
     */
    public function index(Request $request): View
    {
        $query = Property::with([
            'dealType',
            'currency',
            'translations',
            'user',
            'roomCount',
            'propertyType',
            'condition',
            'buildingType',
        ]);

        // ========== Фильтр: Тип сделки ==========
        if ($request->filled('deal_type_id')) {
            $query->where('deal_type_id', $request->deal_type_id);
        }

        // ========== Фильтр: Цена от/до ==========
        if ($request->filled('price_from')) {
            $query->where('price', '>=', $request->price_from);
        }
        if ($request->filled('price_to')) {
            $query->where('price', '<=', $request->price_to);
        }

        // ========== Фильтр: Валюта ==========
        if ($request->filled('currency_id')) {
            $query->where('currency_id', $request->currency_id);
        }

        // ========== Фильтр: Площадь общая от/до ==========
        if ($request->filled('area_from')) {
            $query->where('area_total', '>=', $request->area_from);
        }
        if ($request->filled('area_to')) {
            $query->where('area_total', '<=', $request->area_to);
        }

        // ========== Фильтр: Площадь жилая от/до ==========
        if ($request->filled('area_living_from')) {
            $query->where('area_living', '>=', $request->area_living_from);
        }
        if ($request->filled('area_living_to')) {
            $query->where('area_living', '<=', $request->area_living_to);
        }

        // ========== Фильтр: Площадь кухни от/до ==========
        if ($request->filled('area_kitchen_from')) {
            $query->where('area_kitchen', '>=', $request->area_kitchen_from);
        }
        if ($request->filled('area_kitchen_to')) {
            $query->where('area_kitchen', '<=', $request->area_kitchen_to);
        }

        // ========== Фильтр: Площадь участка от/до ==========
        if ($request->filled('area_land_from')) {
            $query->where('area_land', '>=', $request->area_land_from);
        }
        if ($request->filled('area_land_to')) {
            $query->where('area_land', '<=', $request->area_land_to);
        }

        // ========== Фильтр: Этаж от/до ==========
        if ($request->filled('floor_from')) {
            $query->where('floor', '>=', $request->floor_from);
        }
        if ($request->filled('floor_to')) {
            $query->where('floor', '<=', $request->floor_to);
        }

        // ========== Фильтр: Этажность от/до ==========
        if ($request->filled('floors_total_from')) {
            $query->where('floors_total', '>=', $request->floors_total_from);
        }
        if ($request->filled('floors_total_to')) {
            $query->where('floors_total', '<=', $request->floors_total_to);
        }

        // ========== Фильтр: Цена за м² от/до ==========
        if ($request->filled('price_per_m2_from') || $request->filled('price_per_m2_to')) {
            $query->whereNotNull('price')
                ->whereNotNull('area_total')
                ->where('area_total', '>', 0);

            if ($request->filled('price_per_m2_from')) {
                $query->whereRaw('(price / area_total) >= ?', [$request->price_per_m2_from]);
            }
            if ($request->filled('price_per_m2_to')) {
                $query->whereRaw('(price / area_total) <= ?', [$request->price_per_m2_to]);
            }
        }

        // ========== Фильтр: Количество комнат (множественный выбор) ==========
        if ($request->filled('room_count_id')) {
            $roomCountIds = is_array($request->room_count_id)
                ? $request->room_count_id
                : [$request->room_count_id];
            $query->whereIn('room_count_id', $roomCountIds);
        }

        // ========== Фильтр: Тип недвижимости (множественный выбор) ==========
        if ($request->filled('property_type_id')) {
            $propertyTypeIds = is_array($request->property_type_id)
                ? $request->property_type_id
                : [$request->property_type_id];
            $query->whereIn('property_type_id', $propertyTypeIds);
        }

        // ========== Фильтр: Состояние (множественный выбор) ==========
        if ($request->filled('condition_id')) {
            $conditionIds = is_array($request->condition_id)
                ? $request->condition_id
                : [$request->condition_id];
            $query->whereIn('condition_id', $conditionIds);
        }

        // ========== Фильтр: Тип здания (множественный выбор) ==========
        if ($request->filled('building_type_id')) {
            $buildingTypeIds = is_array($request->building_type_id)
                ? $request->building_type_id
                : [$request->building_type_id];
            $query->whereIn('building_type_id', $buildingTypeIds);
        }

        // ========== Фильтр: Год постройки (множественный выбор) ==========
        if ($request->filled('year_built')) {
            $years = is_array($request->year_built)
                ? $request->year_built
                : [$request->year_built];
            $query->whereIn('year_built', $years);
        }

        // ========== Фильтр: Тип стен (множественный выбор) ==========
        if ($request->filled('wall_type_id')) {
            $wallTypeIds = is_array($request->wall_type_id)
                ? $request->wall_type_id
                : [$request->wall_type_id];
            $query->whereIn('wall_type_id', $wallTypeIds);
        }

        // ========== Фильтр: Отопление (множественный выбор) ==========
        if ($request->filled('heating_type_id')) {
            $heatingTypeIds = is_array($request->heating_type_id)
                ? $request->heating_type_id
                : [$request->heating_type_id];
            $query->whereIn('heating_type_id', $heatingTypeIds);
        }

        // ========== Фильтр: Ванные комнаты (множественный выбор) ==========
        if ($request->filled('bathroom_count_id')) {
            $bathroomCountIds = is_array($request->bathroom_count_id)
                ? $request->bathroom_count_id
                : [$request->bathroom_count_id];
            $query->whereIn('bathroom_count_id', $bathroomCountIds);
        }

        // ========== Фильтр: Высота потолков (множественный выбор) ==========
        if ($request->filled('ceiling_height_id')) {
            $ceilingHeightIds = is_array($request->ceiling_height_id)
                ? $request->ceiling_height_id
                : [$request->ceiling_height_id];
            $query->whereIn('ceiling_height_id', $ceilingHeightIds);
        }

        // ========== Фильтр: Дополнительно / Особенности (множественный выбор) ==========
        if ($request->filled('features')) {
            $featureIds = is_array($request->features)
                ? $request->features
                : [$request->features];
            $query->whereHas('features', function ($q) use ($featureIds) {
                $q->whereIn('dictionaries.id', $featureIds);
            });
        }

        // ========== Фильтр: Девелопер (множественный выбор) ==========
        if ($request->filled('developer_id')) {
            $developerIds = is_array($request->developer_id)
                ? $request->developer_id
                : [$request->developer_id];
            $query->whereHas('complex.developer', function ($q) use ($developerIds) {
                $q->whereIn('developers.id', $developerIds);
            });
        }

        // ========== Фильтр: Статус / Объекты ==========
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'my':
                    $query->where('user_id', auth()->id());
                    break;
                case 'my_company':
                    // TODO: фильтр по компании пользователя
                    break;
                case 'draft':
                    $query->where('status', 'draft');
                    break;
                case 'active':
                    $query->where('status', 'active');
                    break;
                case 'on_review':
                    $query->where('status', 'on_review');
                    break;
                case 'favorite':
                    // TODO: фильтр по избранным
                    break;
                case 'archive':
                    $query->where('status', 'archived');
                    break;
                // 'all' - без фильтра
            }
        }

        // ========== Фильтр: Поиск по ID ==========
        if ($request->filled('search_id')) {
            $query->where('id', $request->search_id);
        }

        // ========== Фильтр: Поиск по контакту ==========
        if ($request->filled('contact_search')) {
            $search = $request->contact_search;
            $query->whereHas('contact', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ========== Фильтр: Дата добавления от/до ==========
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        // Сортировка и пагинация
        $properties = $query->latest()->paginate(20)->withQueryString();

        // Список годов для фильтра
        $years = range(date('Y') + 5, 1950);

        // Данные для фильтров
        return view('pages.properties.index', [
            'properties' => $properties,

            // Справочники
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

            // Другие данные
            'currencies' => Currency::active()->get(),
            'developers' => Developer::active()->orderBy('name')->get(),
            'years' => $years,

            // Текущие значения фильтров
            'filters' => $request->only([
                'deal_type_id',
                'price_from',
                'price_to',
                'currency_id',
                'area_from',
                'area_to',
                'area_living_from',
                'area_living_to',
                'area_kitchen_from',
                'area_kitchen_to',
                'area_land_from',
                'area_land_to',
                'floor_from',
                'floor_to',
                'floors_total_from',
                'floors_total_to',
                'price_per_m2_from',
                'price_per_m2_to',
                'room_count_id',
                'property_type_id',
                'condition_id',
                'building_type_id',
                'year_built',
                'wall_type_id',
                'heating_type_id',
                'bathroom_count_id',
                'ceiling_height_id',
                'features',
                'developer_id',
                'status',
                'search_id',
                'contact_search',
                'created_from',
                'created_to',
            ]),
        ]);
    }

    /**
     * Show the form for creating a new property.
     */
    public function create(): View
    {
        return view('pages.properties.create', [
            // Валюты
            'currencies' => Currency::active()->get(),

            // Источники
            'sources' => Source::active()->orderBy('name')->get(),

            // Комплексы
            'complexes' => Complex::active()->orderBy('name')->get(),

            // Контакты (для модального окна)
            'contacts' => Contact::orderBy('name')->limit(100)->get(),

            // Страны
            'countries' => Country::active()->orderBy('name')->get(),

            // Справочники
            'dealTypes' => Dictionary::getDealTypes(),
            'dealKinds' => Dictionary::getDealKinds(),
            'buildingTypes' => Dictionary::getBuildingTypes(),
            'propertyTypes' => Dictionary::getPropertyTypes(),
            'conditions' => Dictionary::getConditions(),
            'wallTypes' => Dictionary::getWallTypes(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'roomCounts' => Dictionary::getRoomCounts(),
            'bathroomCounts' => Dictionary::getBathroomCounts(),
            'ceilingHeights' => Dictionary::getCeilingHeights(),
            'features' => Dictionary::getFeatures(),

            // Годы постройки (от текущего до 1950)
            'years' => range(date('Y') + 5, 1950),
        ]);
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Валидация полей
        $validated = $request->validate([
            // Required
            'deal_type_id' => 'required|exists:dictionaries,id',
            'currency_id' => 'required|exists:currencies,id',

            // Dictionaries (optional)
            'deal_kind_id' => 'nullable|exists:dictionaries,id',
            'building_type_id' => 'nullable|exists:dictionaries,id',
            'property_type_id' => 'nullable|exists:dictionaries,id',
            'room_count_id' => 'nullable|exists:dictionaries,id',
            'condition_id' => 'nullable|exists:dictionaries,id',
            'bathroom_count_id' => 'nullable|exists:dictionaries,id',
            'ceiling_height_id' => 'nullable|exists:dictionaries,id',
            'wall_type_id' => 'nullable|exists:dictionaries,id',
            'heating_type_id' => 'nullable|exists:dictionaries,id',
            'source_id' => 'nullable|exists:sources,id',

            // Numbers
            'area_total' => 'nullable|numeric|min:0',
            'area_living' => 'nullable|numeric|min:0',
            'area_kitchen' => 'nullable|numeric|min:0',
            'area_land' => 'nullable|numeric|min:0',
            'floor' => 'nullable|integer|min:0',
            'floors_total' => 'nullable|integer|min:1',
            'year_built' => 'nullable|integer|min:1800|max:' . (date('Y') + 10),
            'price' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',

            // Text
            'youtube_url' => 'nullable|url|max:255',
            'title_ru' => 'nullable|string|max:255',
            'agent_notes' => 'nullable|string|max:5000',
            'description_ua' => 'nullable|string|max:10000',
            'description_ru' => 'nullable|string|max:10000',
            'description_en' => 'nullable|string|max:10000',
        ], [
            // Сообщения об ошибках на русском
            'deal_type_id.required' => 'Выберите тип сделки',
            'deal_type_id.exists' => 'Выбранный тип сделки не существует',
            'currency_id.required' => 'Выберите валюту',
            'currency_id.exists' => 'Выбранная валюта не существует',
            'price.numeric' => 'Цена должна быть числом',
            'price.min' => 'Цена не может быть отрицательной',
            'area_total.numeric' => 'Площадь должна быть числом',
            'floor.integer' => 'Этаж должен быть целым числом',
            'floors_total.integer' => 'Этажность должна быть целым числом',
            'youtube_url.url' => 'Введите корректную ссылку на YouTube',
        ]);

        try {
            DB::beginTransaction();

            // ========== Создаем основную запись ==========
            $property = Property::create([
                'user_id' => auth()->id(),

                // Required
                'deal_type_id' => $validated['deal_type_id'],
                'currency_id' => $validated['currency_id'],

                // Dictionaries
                'deal_kind_id' => $validated['deal_kind_id'] ?? null,
                'building_type_id' => $validated['building_type_id'] ?? null,
                'property_type_id' => $validated['property_type_id'] ?? null,
                'room_count_id' => $validated['room_count_id'] ?? null,
                'condition_id' => $validated['condition_id'] ?? null,
                'bathroom_count_id' => $validated['bathroom_count_id'] ?? null,
                'ceiling_height_id' => $validated['ceiling_height_id'] ?? null,
                'wall_type_id' => $validated['wall_type_id'] ?? null,
                'heating_type_id' => $validated['heating_type_id'] ?? null,
                'source_id' => $validated['source_id'] ?? null,

                // Numbers
                'area_total' => $validated['area_total'] ?? null,
                'area_living' => $validated['area_living'] ?? null,
                'area_kitchen' => $validated['area_kitchen'] ?? null,
                'area_land' => $validated['area_land'] ?? null,
                'floor' => $validated['floor'] ?? null,
                'floors_total' => $validated['floors_total'] ?? null,
                'year_built' => $validated['year_built'] ?? null,
                'price' => $validated['price'] ?? null,
                'commission' => $validated['commission'] ?? null,
                'commission_type' => 'percent', // По умолчанию

                // Text
                'youtube_url' => $validated['youtube_url'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,

                // Defaults
                'status' => 'draft',
            ]);

            // ========== Сохраняем переводы ==========
            $this->saveTranslations($property, $validated);

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Объект успешно создан!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании объекта: ' . $e->getMessage());
        }
    }

    /**
     * Save property translations.
     */
    protected function saveTranslations(Property $property, array $validated): void
    {
        $locales = ['ua', 'ru', 'en'];

        foreach ($locales as $locale) {
            $title = $validated['title_ru'] ?? null;
            $description = $validated["description_{$locale}"] ?? null;

            if ($title || $description) {
                PropertyTranslation::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title ?? '',
                        'description' => $description,
                    ]
                );
            }
        }
    }

    /**
     * Save property photos.
     */
    protected function savePhotos(Property $property, array $photos): void
    {
        $sortOrder = 0;

        foreach ($photos as $photo) {
            $sortOrder++;

            $filename = $photo->getClientOriginalName();
            $path = $photo->store("properties/{$property->id}/photos", 'public');

            PropertyPhoto::create([
                'property_id' => $property->id,
                'path' => $path,
                'filename' => $filename,
                'sort_order' => $sortOrder,
                'is_main' => $sortOrder === 1,
            ]);
        }
    }

    /**
     * Save property documents.
     */
    protected function saveDocuments(Property $property, array $documents): void
    {
        foreach ($documents as $document) {
            $filename = $document->getClientOriginalName();
            $path = $document->store("properties/{$property->id}/documents", 'public');

            PropertyDocument::create([
                'property_id' => $property->id,
                'name' => pathinfo($filename, PATHINFO_FILENAME),
                'path' => $path,
                'filename' => $filename,
            ]);
        }
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property): View
    {
        return view('pages.properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property): View
    {
        return view('pages.properties.edit', [
            'property' => $property->load([
                'translations',
                'photos',
                'documents',
                'features',
            ]),

            'currencies' => Currency::active()->get(),
            'sources' => Source::active()->orderBy('name')->get(),
            'complexes' => Complex::active()->orderBy('name')->get(),
            'contacts' => Contact::orderBy('name')->limit(100)->get(),
            'countries' => Country::active()->orderBy('name')->get(),
            'dealTypes' => Dictionary::getDealTypes(),
            'dealKinds' => Dictionary::getDealKinds(),
            'buildingTypes' => Dictionary::getBuildingTypes(),
            'propertyTypes' => Dictionary::getPropertyTypes(),
            'conditions' => Dictionary::getConditions(),
            'wallTypes' => Dictionary::getWallTypes(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'roomCounts' => Dictionary::getRoomCounts(),
            'bathroomCounts' => Dictionary::getBathroomCounts(),
            'ceilingHeights' => Dictionary::getCeilingHeights(),
            'features' => Dictionary::getFeatures(),
            'years' => range(date('Y') + 5, 1950),
        ]);
    }

    /**
     * Update the specified property in storage.
     */
    public function update(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'deal_type_id' => 'required|exists:dictionaries,id',
            'currency_id' => 'required|exists:currencies,id',
            'deal_kind_id' => 'nullable|exists:dictionaries,id',
            'building_type_id' => 'nullable|exists:dictionaries,id',
            'property_type_id' => 'nullable|exists:dictionaries,id',
            'room_count_id' => 'nullable|exists:dictionaries,id',
            'condition_id' => 'nullable|exists:dictionaries,id',
            'bathroom_count_id' => 'nullable|exists:dictionaries,id',
            'ceiling_height_id' => 'nullable|exists:dictionaries,id',
            'wall_type_id' => 'nullable|exists:dictionaries,id',
            'heating_type_id' => 'nullable|exists:dictionaries,id',
            'source_id' => 'nullable|exists:sources,id',
            'area_total' => 'nullable|numeric|min:0',
            'area_living' => 'nullable|numeric|min:0',
            'area_kitchen' => 'nullable|numeric|min:0',
            'area_land' => 'nullable|numeric|min:0',
            'floor' => 'nullable|integer|min:0',
            'floors_total' => 'nullable|integer|min:1',
            'year_built' => 'nullable|integer|min:1800|max:' . (date('Y') + 10),
            'price' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'youtube_url' => 'nullable|url|max:255',
            'title_ru' => 'nullable|string|max:255',
            'agent_notes' => 'nullable|string|max:5000',
            'description_ua' => 'nullable|string|max:10000',
            'description_ru' => 'nullable|string|max:10000',
            'description_en' => 'nullable|string|max:10000',
        ], [
            'deal_type_id.required' => 'Выберите тип сделки',
            'currency_id.required' => 'Выберите валюту',
        ]);

        try {
            DB::beginTransaction();

            $property->update([
                'deal_type_id' => $validated['deal_type_id'],
                'currency_id' => $validated['currency_id'],
                'deal_kind_id' => $validated['deal_kind_id'] ?? null,
                'building_type_id' => $validated['building_type_id'] ?? null,
                'property_type_id' => $validated['property_type_id'] ?? null,
                'room_count_id' => $validated['room_count_id'] ?? null,
                'condition_id' => $validated['condition_id'] ?? null,
                'bathroom_count_id' => $validated['bathroom_count_id'] ?? null,
                'ceiling_height_id' => $validated['ceiling_height_id'] ?? null,
                'wall_type_id' => $validated['wall_type_id'] ?? null,
                'heating_type_id' => $validated['heating_type_id'] ?? null,
                'source_id' => $validated['source_id'] ?? null,
                'area_total' => $validated['area_total'] ?? null,
                'area_living' => $validated['area_living'] ?? null,
                'area_kitchen' => $validated['area_kitchen'] ?? null,
                'area_land' => $validated['area_land'] ?? null,
                'floor' => $validated['floor'] ?? null,
                'floors_total' => $validated['floors_total'] ?? null,
                'year_built' => $validated['year_built'] ?? null,
                'price' => $validated['price'] ?? null,
                'commission' => $validated['commission'] ?? null,
                'youtube_url' => $validated['youtube_url'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
            ]);

            $this->saveTranslations($property, $validated);

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Объект успешно обновлен!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении объекта: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Property $property): RedirectResponse
    {
        try {
            foreach ($property->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }

            foreach ($property->documents as $document) {
                Storage::disk('public')->delete($document->path);
            }

            Storage::disk('public')->deleteDirectory("properties/{$property->id}");

            $property->delete();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Объект успешно удален!');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении объекта: ' . $e->getMessage());
        }
    }
}
