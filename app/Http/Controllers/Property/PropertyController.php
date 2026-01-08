<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
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
use App\Services\PhotoUploadService;
use Illuminate\Http\JsonResponse;
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
        // Список годов для фильтра
        $yearsBuilt = Dictionary::getYearsBuilt();

        // Данные для фильтров (без самих properties - они загрузятся через AJAX)
        return view('pages.properties.index', [
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
            'yearsBuilt' => $yearsBuilt,

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
     * AJAX endpoint для DataTables Server-Side
     */
    public function ajaxData(Request $request): JsonResponse
    {
        // Параметры DataTables
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 20);
        $searchValue = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');
        // ========== Кастомная сортировка ==========
        $sortField = $request->input('sort_field', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';
        // Валидация направления сортировки
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        // Маппинг колонок для сортировки
        $columns = [
            0 => 'id',
            1 => 'id', // location - сортировка по id
            2 => 'deal_type_id',
            3 => 'area_total',
            4 => 'condition_id',
            5 => 'floor',
            6 => 'id', // photo
            7 => 'price',
            8 => 'contact_id',
        ];

        // Валидация поля сортировки
        $allowedSortFields = ['created_at', 'price', 'area_total', 'price_per_m2'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }



        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        // Базовый запрос
        $query = Property::with([
            'dealType',
            'currency',
            'translations',
            'user',
            'roomCount',
            'propertyType',
            'condition',
            'buildingType',
            'photos',
            'contacts.phones',
        ]);

        // ========== Применяем фильтры ==========
        $this->applyFilters($query, $request);

        // ========== Глобальный поиск DataTables ==========
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('id', 'like', "%{$searchValue}%")
                    ->orWhereHas('translations', function ($tq) use ($searchValue) {
                        $tq->where('title', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('street', function ($sq) use ($searchValue) {
                        $sq->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('city', function ($cq) use ($searchValue) {
                        $cq->where('name', 'like', "%{$searchValuajaxDatae}%");
                    });
            });
        }

        // Общее количество записей (без фильтров)
        $recordsTotal = Property::count();

        // Количество после фильтрации
        $recordsFiltered = $query->count();

        // Сортировка
        $query->orderBy($sortField, $sortDir);

// Пагинация
        $properties = $query
            ->skip($start)
            ->take($length)
            ->get();

        // Формируем данные для DataTables
        $data = [];
        foreach ($properties as $property) {
            $data[] = [
                'id' => $property->id,
                'checkbox' => $property->id,
                'location' => '-',
                'property_type' => $property->propertyType?->name ?? '-',
                'room_count' => $property->roomCount?->name ?? null,
                'wall_type' => $property->wallType?->name ?? null,
                'area' => [
                    'total' => $property->area_total ? ceil($property->area_total) : null,
                    'living' => $property->area_living ? ceil($property->area_living) : null,
                    'kitchen' => $property->area_kitchen ? ceil($property->area_kitchen) : null,
                ],
                'area_land' => $property->area_land ? ceil($property->area_land) : null,
                'price_per_m2' => $property->price_per_m2 ? number_format($property->price_per_m2, 0, '.', ' ') : null,
                'condition' => $property->condition?->name ?? '-',
                'floor' => $this->formatFloor($property),
                'photo' => $this->formatPhoto($property),
                'price' => $this->formatPrice($property),
                'contact' => $this->formatContact($property),
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Применение фильтров к запросу
     */
    private function applyFilters($query, Request $request): void
    {
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

        // ========== Фильтр: Особености / Особенности (множественный выбор) ==========
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
            $query->whereHas('contacts', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('inn', 'like', "%{$search}%")
                    ->orWhereHas('phones', function ($pq) use ($search) {
                        $pq->where('phone', 'like', "%{$search}%");
                    });
            });
        }

        // ========== Фильтр: Дата добавления от/до ==========
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }
    }



    /**
     * Форматирование этажа для таблицы
     */
    private function formatFloor(Property $property): string
    {
        if (!$property->floor) {
            return '-';
        }

        $floor = $property->floor;
        if ($property->floors_total) {
            $floor .= '/' . $property->floors_total;
        }

        return $floor;
    }

    /**
     * Форматирование фото для таблицы
     */
    private function formatPhoto(Property $property): string
    {
        $mainPhoto = $property->photos->firstWhere('is_main', true)
            ?? $property->photos->first();

        if ($mainPhoto) {
            return '<img src="' . Storage::url($mainPhoto->path) . '" alt="" class="table-photo" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">';
        }

        return '-';
    }

    /**
     * Форматирование цены для таблицы
     */
    private function formatPrice(Property $property): string
    {
        if (!$property->price) {
            return '-';
        }

        $symbol = $property->currency?->symbol ?? '$';
        return number_format($property->price, 0, '.', ' ') . ' ' . $symbol;
    }

    /**
     * Форматирование контакта для таблицы
     * Возвращает массив данных для рендеринга на клиенте
     */
    private function formatContact(Property $property): array
    {
        $contact = $property->contacts->first();

        if (!$contact) {
            return [
                'has_contact' => false,
            ];
        }

        return [
            'has_contact' => true,
            'full_name' => $contact->full_name,
            'contact_type_name' => $contact->contact_type_name,
            'phone' => $contact->primary_phone,
        ];
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
            'contacts' => Contact::with('phones')->orderBy('last_name')->orderBy('first_name')->limit(100)->get(),

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
            'contactTypes' => Dictionary::getContactTypes(),

            // Годы постройки (от текущего до 1950)
            'yearsBuilt' => Dictionary::getYearsBuilt(),
        ]);
    }

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
        'year_built' => 'nullable|exists:dictionaries,id',
        'price' => 'nullable|numeric|min:0',
        'commission' => 'nullable|numeric|min:0',

        // Text
        'youtube_url' => 'nullable|url|max:255',
        'title_ru' => 'nullable|string|max:255',
        'agent_notes' => 'nullable|string|max:5000',
        'description_ua' => 'nullable|string|max:10000',
        'description_ru' => 'nullable|string|max:10000',
        'description_en' => 'nullable|string|max:10000',

        // Контакты
        'contact_ids' => 'nullable|array',
        'contact_ids.*' => 'exists:contacts,id',

        // Документы
        'documents' => 'nullable|array|max:10',
        'documents.*' => 'file|max:5120',

        // Фотографии
        'photos' => 'nullable|array|max:20',
        'photos.*' => 'file|mimes:jpeg,jpg,png,webp,heic,heif|max:10240',
    ], [
        // Сообщения об ошибках на русском
        'deal_type_id.required' => 'Выберите тип сделки',
        'deal_type_id.exists' => 'Выбранный тип сделки не существует',
        'currency_id.required' => 'Выберите валюту',
        'currency_id.exists' => 'Выбранная валюта не существует',
        'price.numeric' => 'Цена должна быть числом',
        'price.min' => 'Цена не может быть отрицательной',
        'area_total.numeric' => 'Площадь должна быть числом',
        'year_built.exists' => 'Выбранный год постройки не существует',
        'floors_total.integer' => 'Этажность должна быть целым числом',
        'youtube_url.url' => 'Введите корректную ссылку на YouTube',
        'contact_ids.array' => 'Неверный формат контактов',
        'contact_ids.*.exists' => 'Выбранный контакт не существует',
        'documents.array' => 'Неверный формат документов',
        'documents.max' => 'Максимум 10 документов',
        'documents.*.file' => 'Ошибка загрузки файла',
        'documents.*.mimes' => 'Разрешены только файлы: PNG, JPEG, PDF',
        'documents.*.max' => 'Максимальный размер файла 5MB',
        // Фото
        'photos.array' => 'Неверный формат фотографий',
        'photos.max' => 'Максимум 20 фотографий',
        'photos.*.file' => 'Ошибка загрузки фото',
        'photos.*.mimes' => 'Разрешены только: JPEG, PNG, WebP, HEIC',
        'photos.*.max' => 'Максимальный размер фото 10MB',
    ]);

    try {
        DB::beginTransaction();

        // Вычисляем цену за м²
        $pricePerM2 = null;
        if (!empty($validated['price']) && !empty($validated['area_total']) && $validated['area_total'] > 0) {
            $pricePerM2 = $validated['price'] / $validated['area_total'];
        }

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
            'price_per_m2' => $pricePerM2,
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

        // ========== Привязываем контакты ==========
        if (!empty($validated['contact_ids'])) {
            $property->contacts()->attach($validated['contact_ids']);
        }

        // После создания property:
        if ($request->has('features')) {
            $property->features()->sync($request->input('features', []));
        }

        // ========== Сохраняем документы ==========
        if ($request->hasFile('documents')) {
            $this->saveDocuments($property, $request->file('documents'));
        }

        // ========== Сохраняем фотографии ==========
        if ($request->hasFile('photos')) {
            $photoService = app(PhotoUploadService::class);
            $photoService->uploadPhotos($property, $request->file('photos'));
        }

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
     * Сохранение документов объекта
     */
    protected function saveDocuments(Property $property, array $files): void
    {
        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();
            $path = $file->store("/properties/{$property->id}/documents");

            $property->documents()->create([
                'name' => pathinfo($filename, PATHINFO_FILENAME),
                'filename' => $filename,
                'path' => $path,
            ]);
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
     * Сохранить фотографии объекта
     *
     * @param Property $property
     * @param array $files
     * @return void
     */
    protected function savePhotos(Property $property, array $files): void
    {
        $photoService = app(PhotoUploadService::class);
        $photoService->uploadPhotos($property, $files);
    }








}
