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

        // ========== Фильтр: Площадь от/до ==========
        if ($request->filled('area_from')) {
            $query->where('area_total', '>=', $request->area_from);
        }
        if ($request->filled('area_to')) {
            $query->where('area_total', '<=', $request->area_to);
        }

        // ========== Фильтр: Количество комнат ==========
        if ($request->filled('room_count_id')) {
            $query->where('room_count_id', $request->room_count_id);
        }

        // ========== Фильтр: Статус ==========
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'my':
                    $query->where('user_id', auth()->id());
                    break;
                case 'draft':
                    $query->where('status', 'draft');
                    break;
                case 'active':
                    $query->where('status', 'active');
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

        // Сортировка и пагинация
        $properties = $query->latest()->paginate(20)->withQueryString();

        // Данные для фильтров
        return view('pages.properties.index', [
            'properties' => $properties,
            'dealTypes' => Dictionary::getDealTypes(),
            'currencies' => Currency::active()->get(),
            'roomCounts' => Dictionary::getRoomCounts(),
            'filters' => $request->only([
                'deal_type_id',
                'price_from',
                'price_to',
                'currency_id',
                'area_from',
                'area_to',
                'room_count_id',
                'status',
                'search_id',
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

                // ========== ПОТОМ: Location ==========
                // 'contact_id' => $validated['contact_id'] ?? null,
                // 'complex_id' => $validated['complex_id'] ?? null,
                // 'section_id' => $validated['section_id'] ?? null,
                // 'country_id' => $validated['country_id'] ?? null,
                // 'region_id' => $validated['region_id'] ?? null,
                // 'city_id' => $validated['city_id'] ?? null,
                // 'district_id' => $validated['district_id'] ?? null,
                // 'zone_id' => $validated['zone_id'] ?? null,
                // 'street_id' => $validated['street_id'] ?? null,
                // 'landmark_id' => $validated['landmark_id'] ?? null,
                // 'building_number' => $validated['building_number'] ?? null,
                // 'apartment_number' => $validated['apartment_number'] ?? null,
                // 'location_name' => $validated['location_name'] ?? null,
                // 'latitude' => $validated['latitude'] ?? null,
                // 'longitude' => $validated['longitude'] ?? null,
                // 'external_url' => $validated['external_url'] ?? null,
                // 'is_visible_to_agents' => $validated['is_visible_to_agents'] ?? false,
                // 'notes' => $validated['notes'] ?? null,
            ]);

            // ========== Сохраняем переводы ==========
            $this->saveTranslations($property, $validated);

            // ========== ПОТОМ: Особенности ==========
            // if (!empty($validated['features'])) {
            //     $property->features()->sync($validated['features']);
            // }

            // ========== ПОТОМ: Фото ==========
            // if ($request->hasFile('photos')) {
            //     $this->savePhotos($property, $request->file('photos'));
            // }

            // ========== ПОТОМ: Документы ==========
            // if ($request->hasFile('documents')) {
            //     $this->saveDocuments($property, $request->file('documents'));
            // }

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
            // Для title используем title_ru для всех локалей (пока)
            $title = $validated['title_ru'] ?? null;
            $description = $validated["description_{$locale}"] ?? null;

            // Сохраняем только если есть хотя бы заголовок или описание
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

            // Генерируем уникальное имя файла
            $filename = $photo->getClientOriginalName();
            $path = $photo->store("properties/{$property->id}/photos", 'public');

            PropertyPhoto::create([
                'property_id' => $property->id,
                'path' => $path,
                'filename' => $filename,
                'sort_order' => $sortOrder,
                'is_main' => $sortOrder === 1, // Первое фото - главное
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

            // Те же данные что и в create
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
            'currency_id.required' => 'Выберите валюту',
        ]);

        try {
            DB::beginTransaction();

            // Обновляем основные данные
            $property->update([
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

                // Text
                'youtube_url' => $validated['youtube_url'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
            ]);

            // Обновляем переводы
            $this->saveTranslations($property, $validated);

            // ========== ПОТОМ: Особенности ==========
            // $property->features()->sync($validated['features'] ?? []);

            // ========== ПОТОМ: Фото ==========
            // if ($request->hasFile('photos')) {
            //     $this->savePhotos($property, $request->file('photos'));
            // }

            // ========== ПОТОМ: Документы ==========
            // if ($request->hasFile('documents')) {
            //     $this->saveDocuments($property, $request->file('documents'));
            // }

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
            // Удаляем файлы фото
            foreach ($property->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }

            // Удаляем файлы документов
            foreach ($property->documents as $document) {
                Storage::disk('public')->delete($document->path);
            }

            // Удаляем папку объекта
            Storage::disk('public')->deleteDirectory("properties/{$property->id}");

            // Soft delete
            $property->delete();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Объект успешно удален!');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении объекта: ' . $e->getMessage());
        }
    }
}
