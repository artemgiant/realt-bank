<?php

namespace App\Http\Controllers;

use App\Models\Contact\Contact;
use App\Models\Reference\Developer;
use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use App\Models\Reference\Dictionary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComplexController extends Controller
{
    /**
     * Display a listing of complexes.
     */
    public function index(Request $request): View
    {
        return view('pages.complexes.index', [
            // Справочники для фильтров
            'categories' => collect([
                (object)['id' => 1, 'name' => 'Жилая недвижимость'],
                (object)['id' => 2, 'name' => 'Коммерческая недвижимость'],
                (object)['id' => 3, 'name' => 'Загородная недвижимость'],
            ]),
            'objectTypes' => collect([
                (object)['id' => 1, 'name' => 'Квартира'],
                (object)['id' => 2, 'name' => 'Дом'],
                (object)['id' => 3, 'name' => 'Таунхаус'],
                (object)['id' => 4, 'name' => 'Пентхаус'],
            ]),
            'developers' => Developer::orderBy('name')->get(),
            'housingClasses' => Dictionary::getHousingClasses(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'wallTypes' => Dictionary::getWallTypes(),
            'yearsBuilt' => Dictionary::getYearsBuilt(),
            'conditions' => Dictionary::getConditions(),
            'features' => Dictionary::getFeatures(),
        ]);
    }

    /**
     * Show the form for creating a new complex.
     */
    public function create(): View
    {
        return view('pages.complexes.create', [
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),
            'housingClasses' => Dictionary::getHousingClasses(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'wallTypes' => Dictionary::getWallTypes(),
            'yearsBuilt' => Dictionary::getYearsBuilt(),
            'features' => Dictionary::getFeatures(),
            'conditions' => Dictionary::getConditions(),
            // Тестовые данные для категорий и типов объектов
            'categories' => collect([
                (object)['id' => 1, 'name' => 'Жилая недвижимость'],
                (object)['id' => 2, 'name' => 'Коммерческая недвижимость'],
                (object)['id' => 3, 'name' => 'Загородная недвижимость'],
            ]),
            'objectTypes' => collect([
                (object)['id' => 1, 'name' => 'Квартира'],
                (object)['id' => 2, 'name' => 'Дом'],
                (object)['id' => 3, 'name' => 'Таунхаус'],
                (object)['id' => 4, 'name' => 'Пентхаус'],
            ]),
        ]);
    }

    /**
     * Store a newly created complex.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Названия (мультиязычные)
            'name_ua' => 'nullable|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',

            // Описания (мультиязычные)
            'description_ua' => 'nullable|string|max:10000',
            'description_ru' => 'nullable|string|max:10000',
            'description_en' => 'nullable|string|max:10000',

            // Основные поля
            'developer_id' => 'nullable|exists:developers,id',
            'website' => 'nullable|url|max:255',
            'company_website' => 'nullable|url|max:255',
            'materials_url' => 'nullable|url|max:255',
            'agent_notes' => 'nullable|string|max:5000',
            'special_conditions' => 'nullable|string|max:5000',
            'housing_class_id' => 'nullable|exists:dictionaries,id',

            // Локация
            'city_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'zone_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',

            // Контакты (максимум 5)
            'contact_ids' => 'nullable|array|max:5',
            'contact_ids.*' => 'exists:contacts,id',

            // Файлы
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'plans' => 'nullable|array|max:10',
            'plans.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',

            // Площадь и цена
            'area_from' => 'nullable|numeric|min:0',
            'area_to' => 'nullable|numeric|min:0',
            'price_per_m2' => 'nullable|numeric|min:0',
            'price_total' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:USD,UAH,EUR',

            // Категория и тип объекта
            'category_id' => 'nullable|integer',
            'object_type_id' => 'nullable|integer',
            'objects_count' => 'nullable|integer|min:0',

            // Состояния и особенности
            'conditions' => 'nullable|array',
            'conditions.*' => 'exists:dictionaries,id',
            'features' => 'nullable|array',
            'features.*' => 'exists:dictionaries,id',

            // Блоки
            'blocks' => 'nullable|array',
            'blocks.*.name' => 'nullable|string|max:255',
            'blocks.*.street_id' => 'nullable|integer',
            'blocks.*.building_number' => 'nullable|string|max:50',
            'blocks.*.floors_total' => 'nullable|integer|min:1|max:200',
            'blocks.*.year_built' => 'nullable|integer|min:1900|max:2100',
            'blocks.*.heating_type_id' => 'nullable|exists:dictionaries,id',
            'blocks.*.wall_type_id' => 'nullable|exists:dictionaries,id',
        ], [
            'name_ua.max' => 'Назва (UA) занадто довга',
            'name_ru.max' => 'Название (RU) слишком длинное',
            'name_en.max' => 'Name (EN) is too long',
            'website.url' => 'Введите корректную ссылку на сайт комплекса',
            'company_website.url' => 'Введите корректную ссылку на сайт компании',
            'materials_url.url' => 'Введите корректную ссылку на материалы',
            'logo.image' => 'Логотип должен быть изображением',
            'logo.mimes' => 'Разрешены только: JPEG, PNG, WebP',
            'logo.max' => 'Максимальный размер логотипа 2MB',
            'photos.*.image' => 'Файл должен быть изображением',
            'photos.*.max' => 'Максимальный размер фото 5MB',
            'plans.*.image' => 'План должен быть изображением',
            'plans.*.max' => 'Максимальный размер плана 5MB',
            'contact_ids.max' => 'Максимум 5 контактов',
            'photos.max' => 'Максимум 10 фото',
            'plans.max' => 'Максимум 10 планов',
        ]);

        try {
            DB::beginTransaction();

            // Определяем основное название (приоритет: RU -> UA -> EN)
            $mainName = $validated['name_ru'] ?? $validated['name_ua'] ?? $validated['name_en'] ?? 'Без названия';

            // Подготавливаем мультиязычные данные
            $nameTranslations = array_filter([
                'ua' => $validated['name_ua'] ?? null,
                'ru' => $validated['name_ru'] ?? null,
                'en' => $validated['name_en'] ?? null,
            ]);

            $descriptionTranslations = array_filter([
                'ua' => $validated['description_ua'] ?? null,
                'ru' => $validated['description_ru'] ?? null,
                'en' => $validated['description_en'] ?? null,
            ]);

            // Создаем комплекс
            $complex = Complex::create([
                'name' => $mainName,
                'slug' => Str::slug($mainName),
                'developer_id' => $validated['developer_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'zone_id' => $validated['zone_id'] ?? null,
                'website' => $validated['website'] ?? null,
                'company_website' => $validated['company_website'] ?? null,
                'materials_url' => $validated['materials_url'] ?? null,
                'description' => $validated['description_ru'] ?? $validated['description_ua'] ?? $validated['description_en'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
                'special_conditions' => $validated['special_conditions'] ?? null,
                'housing_class_id' => $validated['housing_class_id'] ?? null,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
                'is_active' => true,
                // Новые поля
                'area_from' => $validated['area_from'] ?? null,
                'area_to' => $validated['area_to'] ?? null,
                'price_per_m2' => $validated['price_per_m2'] ?? null,
                'price_total' => $validated['price_total'] ?? null,
                'currency' => $validated['currency'] ?? 'USD',
                'category_id' => $validated['category_id'] ?? null,
                'object_type_id' => $validated['object_type_id'] ?? null,
                'objects_count' => $validated['objects_count'] ?? null,
                'conditions' => $validated['conditions'] ?? null,
                'features' => $validated['features'] ?? null,
            ]);

            // Привязываем контакты через полиморфную связь
            if (!empty($validated['contact_ids'])) {
                $contactData = [];
                foreach ($validated['contact_ids'] as $index => $id) {
                    $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
                }
                $complex->contacts()->attach($contactData);
            }

            // Сохраняем логотип
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store("complexes/{$complex->id}/logo", 'public');
                $complex->update(['logo_path' => $logoPath]);
            }

            // Сохраняем фото комплекса в JSON
            if ($request->hasFile('photos')) {
                $photoPaths = [];
                foreach ($request->file('photos') as $photo) {
                    $photoPaths[] = $photo->store("complexes/{$complex->id}/photos", 'public');
                }
                $complex->update(['photos' => $photoPaths]);
            }

            // Сохраняем планы комплекса в JSON
            if ($request->hasFile('plans')) {
                $planPaths = [];
                foreach ($request->file('plans') as $plan) {
                    $planPaths[] = $plan->store("complexes/{$complex->id}/plans", 'public');
                }
                $complex->update(['plans' => $planPaths]);
            }

            // Сохраняем блоки
            $this->saveBlocks($complex, $validated['blocks'] ?? [], $request);

            DB::commit();

            return redirect()
                ->route('complexes.index')
                ->with('success', 'Комплекс успешно создан!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании комплекса: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified complex.
     */
    public function show(Complex $complex): View
    {
        $complex->load(['developer', 'contacts.phones', 'blocks.street', 'housingClass', 'city', 'district', 'zone']);

        return view('pages.complexes.show', [
            'complex' => $complex,
        ]);
    }

    /**
     * Show the form for editing the specified complex.
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
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),
            'housingClasses' => Dictionary::getHousingClasses(),
            'heatingTypes' => Dictionary::getHeatingTypes(),
            'wallTypes' => Dictionary::getWallTypes(),
            'yearsBuilt' => Dictionary::getYearsBuilt(),
            'features' => Dictionary::getFeatures(),
            'conditions' => Dictionary::getConditions(),
            // Тестовые данные для категорий и типов объектов
            'categories' => collect([
                (object)['id' => 1, 'name' => 'Жилая недвижимость'],
                (object)['id' => 2, 'name' => 'Коммерческая недвижимость'],
                (object)['id' => 3, 'name' => 'Загородная недвижимость'],
            ]),
            'objectTypes' => collect([
                (object)['id' => 1, 'name' => 'Квартира'],
                (object)['id' => 2, 'name' => 'Дом'],
                (object)['id' => 3, 'name' => 'Таунхаус'],
                (object)['id' => 4, 'name' => 'Пентхаус'],
            ]),
        ]);
    }

    /**
     * Update the specified complex.
     */
    public function update(Request $request, Complex $complex): RedirectResponse
    {
        $validated = $request->validate([
            // Названия (мультиязычные)
            'name_ua' => 'nullable|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',

            // Описания (мультиязычные)
            'description_ua' => 'nullable|string|max:10000',
            'description_ru' => 'nullable|string|max:10000',
            'description_en' => 'nullable|string|max:10000',

            // Основные поля
            'developer_id' => 'nullable|exists:developers,id',
            'website' => 'nullable|url|max:255',
            'company_website' => 'nullable|url|max:255',
            'materials_url' => 'nullable|url|max:255',
            'agent_notes' => 'nullable|string|max:5000',
            'special_conditions' => 'nullable|string|max:5000',
            'housing_class_id' => 'nullable|exists:dictionaries,id',

            // Локация
            'city_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'zone_id' => 'nullable|integer',

            // Контакты
            'contact_ids' => 'nullable|array',
            'contact_ids.*' => 'exists:contacts,id',

            // Файлы
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'plans' => 'nullable|array',
            'plans.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',

            // Удаление медиа
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer',
            'delete_plans' => 'nullable|array',
            'delete_plans.*' => 'integer',

            // Блоки
            'blocks' => 'nullable|array',
            'blocks.*.id' => 'nullable|integer',
            'blocks.*.name' => 'nullable|string|max:255',
            'blocks.*.street_id' => 'nullable|integer',
            'blocks.*.building_number' => 'nullable|string|max:50',
            'blocks.*.floors_total' => 'nullable|integer|min:1|max:200',
            'blocks.*.year_built' => 'nullable|integer|min:1900|max:2100',
            'blocks.*.heating_type_id' => 'nullable|exists:dictionaries,id',
            'blocks.*.wall_type_id' => 'nullable|exists:dictionaries,id',
            'blocks.*.delete' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Определяем основное название (приоритет: RU -> UA -> EN)
            $mainName = $validated['name_ru'] ?? $validated['name_ua'] ?? $validated['name_en'] ?? $complex->name;

            // Подготавливаем мультиязычные данные
            $nameTranslations = array_filter([
                'ua' => $validated['name_ua'] ?? null,
                'ru' => $validated['name_ru'] ?? null,
                'en' => $validated['name_en'] ?? null,
            ]);

            $descriptionTranslations = array_filter([
                'ua' => $validated['description_ua'] ?? null,
                'ru' => $validated['description_ru'] ?? null,
                'en' => $validated['description_en'] ?? null,
            ]);

            // Обновляем комплекс
            $complex->update([
                'name' => $mainName,
                'slug' => Str::slug($mainName),
                'developer_id' => $validated['developer_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'zone_id' => $validated['zone_id'] ?? null,
                'website' => $validated['website'] ?? null,
                'company_website' => $validated['company_website'] ?? null,
                'materials_url' => $validated['materials_url'] ?? null,
                'description' => $validated['description_ru'] ?? $validated['description_ua'] ?? $validated['description_en'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
                'special_conditions' => $validated['special_conditions'] ?? null,
                'housing_class_id' => $validated['housing_class_id'] ?? null,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
            ]);

            // Обновляем контакты
            $complex->contacts()->detach();
            if (!empty($validated['contact_ids'])) {
                $contactData = [];
                foreach ($validated['contact_ids'] as $index => $id) {
                    $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
                }
                $complex->contacts()->attach($contactData);
            }

            // Обновляем логотип
            if ($request->hasFile('logo')) {
                // Удаляем старый логотип
                if ($complex->logo_path) {
                    Storage::disk('public')->delete($complex->logo_path);
                }
                $logoPath = $request->file('logo')->store("complexes/{$complex->id}/logo", 'public');
                $complex->update(['logo_path' => $logoPath]);
            }

            // Удаляем помеченные фото (по индексу в JSON массиве)
            if (!empty($validated['delete_photos'])) {
                $existingPhotos = $complex->photos ?? [];
                foreach ($validated['delete_photos'] as $index) {
                    if (isset($existingPhotos[$index])) {
                        Storage::disk('public')->delete($existingPhotos[$index]);
                        unset($existingPhotos[$index]);
                    }
                }
                $complex->update(['photos' => array_values($existingPhotos)]);
            }

            // Удаляем помеченные планы (по индексу в JSON массиве)
            if (!empty($validated['delete_plans'])) {
                $existingPlans = $complex->plans ?? [];
                foreach ($validated['delete_plans'] as $index) {
                    if (isset($existingPlans[$index])) {
                        Storage::disk('public')->delete($existingPlans[$index]);
                        unset($existingPlans[$index]);
                    }
                }
                $complex->update(['plans' => array_values($existingPlans)]);
            }

            // Добавляем новые фото в JSON массив
            if ($request->hasFile('photos')) {
                $existingPhotos = $complex->photos ?? [];
                foreach ($request->file('photos') as $photo) {
                    $existingPhotos[] = $photo->store("complexes/{$complex->id}/photos", 'public');
                }
                $complex->update(['photos' => $existingPhotos]);
            }

            // Добавляем новые планы в JSON массив
            if ($request->hasFile('plans')) {
                $existingPlans = $complex->plans ?? [];
                foreach ($request->file('plans') as $plan) {
                    $existingPlans[] = $plan->store("complexes/{$complex->id}/plans", 'public');
                }
                $complex->update(['plans' => $existingPlans]);
            }

            // Обновляем блоки
            $this->updateBlocks($complex, $validated['blocks'] ?? [], $request);

            DB::commit();

            return redirect()
                ->route('complexes.index')
                ->with('success', 'Комплекс успешно обновлен!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении комплекса: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified complex.
     */
    public function destroy(Complex $complex): RedirectResponse
    {
        try {
            // Удаляем логотип
            if ($complex->logo_path) {
                Storage::disk('public')->delete($complex->logo_path);
            }

            // Удаляем фото из JSON
            if ($complex->photos) {
                foreach ($complex->photos as $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
            }

            // Удаляем планы из JSON
            if ($complex->plans) {
                foreach ($complex->plans as $planPath) {
                    Storage::disk('public')->delete($planPath);
                }
            }

            // Удаляем планы блоков
            foreach ($complex->blocks as $block) {
                if ($block->plan_path) {
                    Storage::disk('public')->delete($block->plan_path);
                }
            }

            // Удаляем директорию комплекса
            Storage::disk('public')->deleteDirectory("complexes/{$complex->id}");

            $complex->delete();

            return redirect()
                ->route('complexes.index')
                ->with('success', 'Комплекс успешно удален!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении комплекса: ' . $e->getMessage());
        }
    }

    /**
     * Сохранение блоков комплекса
     */
    private function saveBlocks(Complex $complex, array $blocksData, Request $request): void
    {
        foreach ($blocksData as $index => $blockData) {
            if (empty($blockData['name'])) {
                continue;
            }

            $block = Block::create([
                'name' => $blockData['name'],
                'slug' => Str::slug($blockData['name']),
                'complex_id' => $complex->id,
                'developer_id' => $complex->developer_id,
                'street_id' => $blockData['street_id'] ?? null,
                'building_number' => $blockData['building_number'] ?? null,
                'floors_total' => $blockData['floors_total'] ?? null,
                'year_built' => $blockData['year_built'] ?? null,
                'heating_type_id' => $blockData['heating_type_id'] ?? null,
                'wall_type_id' => $blockData['wall_type_id'] ?? null,
                'is_active' => true,
            ]);

            // Сохраняем план блока
            $planKey = "blocks.{$index}.plan";
            if ($request->hasFile($planKey)) {
                $planPath = $request->file($planKey)->store("complexes/{$complex->id}/blocks/{$block->id}", 'public');
                $block->update(['plan_path' => $planPath]);
            }
        }
    }

    /**
     * Обновление блоков комплекса
     */
    private function updateBlocks(Complex $complex, array $blocksData, Request $request): void
    {
        $existingBlockIds = [];

        foreach ($blocksData as $index => $blockData) {
            // Пропускаем блоки помеченные на удаление
            if (!empty($blockData['delete'])) {
                if (!empty($blockData['id'])) {
                    $block = Block::find($blockData['id']);
                    if ($block) {
                        if ($block->plan_path) {
                            Storage::disk('public')->delete($block->plan_path);
                        }
                        $block->delete();
                    }
                }
                continue;
            }

            if (empty($blockData['name'])) {
                continue;
            }

            if (!empty($blockData['id'])) {
                // Обновляем существующий блок
                $block = Block::find($blockData['id']);
                if ($block) {
                    $block->update([
                        'name' => $blockData['name'],
                        'slug' => Str::slug($blockData['name']),
                        'street_id' => $blockData['street_id'] ?? null,
                        'building_number' => $blockData['building_number'] ?? null,
                        'floors_total' => $blockData['floors_total'] ?? null,
                        'year_built' => $blockData['year_built'] ?? null,
                        'heating_type_id' => $blockData['heating_type_id'] ?? null,
                        'wall_type_id' => $blockData['wall_type_id'] ?? null,
                    ]);
                    $existingBlockIds[] = $block->id;
                }
            } else {
                // Создаем новый блок
                $block = Block::create([
                    'name' => $blockData['name'],
                    'slug' => Str::slug($blockData['name']),
                    'complex_id' => $complex->id,
                    'developer_id' => $complex->developer_id,
                    'street_id' => $blockData['street_id'] ?? null,
                    'building_number' => $blockData['building_number'] ?? null,
                    'floors_total' => $blockData['floors_total'] ?? null,
                    'year_built' => $blockData['year_built'] ?? null,
                    'heating_type_id' => $blockData['heating_type_id'] ?? null,
                    'wall_type_id' => $blockData['wall_type_id'] ?? null,
                    'is_active' => true,
                ]);
                $existingBlockIds[] = $block->id;
            }

            // Обновляем план блока
            $planKey = "blocks.{$index}.plan";
            if ($request->hasFile($planKey)) {
                if ($block->plan_path) {
                    Storage::disk('public')->delete($block->plan_path);
                }
                $planPath = $request->file($planKey)->store("complexes/{$complex->id}/blocks/{$block->id}", 'public');
                $block->update(['plan_path' => $planPath]);
            }
        }
    }

    // ========== AJAX Methods ==========

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
                'developer_name' => $complex->developer ? $complex->developer->name : '-',
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

        $query = Block::with(['street.zone', 'street.district', 'street.city'])
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
                'street_name' => $street ? $street->name : '',
                'building_number' => $block->building_number ?? '',
                'zone_id' => $street ? $street->zone_id : null,
                'zone_name' => ($street && $street->zone) ? $street->zone->name : '',
                'district_id' => $street ? $street->district_id : null,
                'district_name' => ($street && $street->district) ? $street->district->name : '',
                'city_id' => $street ? $street->city_id : null,
                'city_name' => ($street && $street->city) ? $street->city->name : '',
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
     * AJAX данные для DataTables
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $query = Complex::with([
            'developer',
            'city.state.country',
            'district',
            'zone',
            'blocks.heatingType',
            'blocks.wallType',
            'blocks.street',
            'contacts.phones',
            'housingClass',
        ]);

        // ========== Фильтры ==========

        // Поиск по ID
        if ($searchId = $request->get('search_id')) {
            $query->where('id', $searchId);
        }

        // Фильтр по категории
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Фильтр по девелоперу
        if ($developerId = $request->get('developer_id')) {
            $query->where('developer_id', $developerId);
        }

        // Фильтр по классу жилья
        if ($housingClassId = $request->get('housing_class_id')) {
            $query->where('housing_class_id', $housingClassId);
        }

        // Фильтр по типу объекта (множественный)
        if ($objectTypeIds = $request->get('object_type_id')) {
            if (is_array($objectTypeIds)) {
                $query->whereIn('object_type_id', $objectTypeIds);
            }
        }

        // Фильтр по году сдачи (через блоки)
        if ($yearsBuilt = $request->get('year_built')) {
            if (is_array($yearsBuilt)) {
                $query->whereHas('blocks', function ($q) use ($yearsBuilt) {
                    $q->whereIn('year_built', $yearsBuilt);
                });
            }
        }

        // Фильтр по состоянию
        if ($conditionIds = $request->get('condition_id')) {
            if (is_array($conditionIds)) {
                $query->where(function ($q) use ($conditionIds) {
                    foreach ($conditionIds as $conditionId) {
                        $q->orWhereJsonContains('conditions', (int) $conditionId);
                    }
                });
            }
        }

        // Фильтр по типу стен (через блоки)
        if ($wallTypeIds = $request->get('wall_type_id')) {
            if (is_array($wallTypeIds)) {
                $query->whereHas('blocks', function ($q) use ($wallTypeIds) {
                    $q->whereIn('wall_type_id', $wallTypeIds);
                });
            }
        }

        // Фильтр по отоплению (через блоки)
        if ($heatingTypeIds = $request->get('heating_type_id')) {
            if (is_array($heatingTypeIds)) {
                $query->whereHas('blocks', function ($q) use ($heatingTypeIds) {
                    $q->whereIn('heating_type_id', $heatingTypeIds);
                });
            }
        }

        // Фильтр по особенностям
        if ($featureIds = $request->get('features')) {
            if (is_array($featureIds)) {
                $query->where(function ($q) use ($featureIds) {
                    foreach ($featureIds as $featureId) {
                        $q->whereJsonContains('features', (int) $featureId);
                    }
                });
            }
        }

        // Фильтр по площади
        if ($areaFrom = $request->get('area_from')) {
            $query->where('area_from', '>=', $areaFrom);
        }
        if ($areaTo = $request->get('area_to')) {
            $query->where('area_to', '<=', $areaTo);
        }

        // Фильтр по этажности (через блоки)
        if ($floorsFrom = $request->get('floors_from')) {
            $query->whereHas('blocks', function ($q) use ($floorsFrom) {
                $q->where('floors_total', '>=', $floorsFrom);
            });
        }
        if ($floorsTo = $request->get('floors_to')) {
            $query->whereHas('blocks', function ($q) use ($floorsTo) {
                $q->where('floors_total', '<=', $floorsTo);
            });
        }

        // Фильтр по цене
        if ($priceFrom = $request->get('price_from')) {
            $query->where('price_total', '>=', $priceFrom);
        }
        if ($priceTo = $request->get('price_to')) {
            $query->where('price_total', '<=', $priceTo);
        }

        // Фильтр по цене за м²
        if ($pricePerM2From = $request->get('price_per_m2_from')) {
            $query->where('price_per_m2', '>=', $pricePerM2From);
        }
        if ($pricePerM2To = $request->get('price_per_m2_to')) {
            $query->where('price_per_m2', '<=', $pricePerM2To);
        }

        // Фильтр по локации
        if ($locationType = $request->get('location_type')) {
            $locationId = $request->get('location_id');
            if ($locationId) {
                switch ($locationType) {
                    case 'country':
                        $query->whereHas('city.state', function ($q) use ($locationId) {
                            $q->where('country_id', $locationId);
                        });
                        break;
                    case 'region':
                        $query->whereHas('city', function ($q) use ($locationId) {
                            $q->where('state_id', $locationId);
                        });
                        break;
                    case 'city':
                        $query->where('city_id', $locationId);
                        break;
                }
            }
        }

        // Детальные фильтры локации (районы, улицы, и т.д.)
        if ($detailIds = $request->get('detail_ids')) {
            $details = json_decode($detailIds, true);
            if (is_array($details) && count($details) > 0) {
                $query->where(function ($q) use ($details) {
                    foreach ($details as $detail) {
                        switch ($detail['type']) {
                            case 'district':
                                $q->orWhere('district_id', $detail['id']);
                                break;
                            case 'developer':
                                $q->orWhere('developer_id', $detail['id']);
                                break;
                        }
                    }
                });
            }
        }

        // Общее количество до пагинации
        $recordsTotal = Complex::count();
        $recordsFiltered = $query->count();

        // ========== Сортировка ==========
        $sortField = $request->get('sort_field', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $allowedSortFields = ['created_at', 'name', 'price_per_m2', 'area_from', 'price_total'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // ========== Пагинация (DataTables format) ==========
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $complexes = $query->skip($start)->take($length)->get();

        // ========== Форматирование данных для рендеров ==========
        $data = $complexes->map(function ($complex) {
            // Получаем первый контакт
            $contact = $complex->contacts->first();
            $contactPhone = $contact && $contact->phones->isNotEmpty()
                ? $contact->phones->first()->phone
                : null;

            // Получаем годы сдачи из блоков
            $years = $complex->blocks->pluck('year_built')->filter()->unique()->sort();
            $yearsStr = $years->isNotEmpty()
                ? ($years->count() > 1 ? $years->min() . '-' . $years->max() : $years->first())
                : null;

            // Получаем диапазон этажности
            $floors = $complex->blocks->pluck('floors_total')->filter();
            $floorsStr = null;
            if ($floors->isNotEmpty()) {
                if ($floors->min() === $floors->max()) {
                    $floorsStr = $floors->min();
                } else {
                    $floorsStr = $floors->min() . '-' . $floors->max();
                }
            }

            // Условия/состояния
            $conditionNames = [];
            if ($complex->conditions) {
                $conditionIds = is_array($complex->conditions) ? $complex->conditions : json_decode($complex->conditions, true);
                if ($conditionIds) {
                    $conditionNames = Dictionary::whereIn('id', $conditionIds)->pluck('name')->toArray();
                }
            }

            // Тип стен из блоков
            $wallTypes = $complex->blocks->pluck('wallType.name')->filter()->unique()->toArray();

            // Особенности
            $featureNames = [];
            if ($complex->features) {
                $featureIds = is_array($complex->features) ? $complex->features : json_decode($complex->features, true);
                if ($featureIds) {
                    $featureNames = Dictionary::whereIn('id', $featureIds)->pluck('name')->toArray();
                }
            }

            // Формат для рендеров
            return [
                'id' => $complex->id,
                'checkbox' => $complex->id,

                // Локация
                'location' => [
                    'has_location' => true,
                    'name' => $complex->name,
                    'years' => $yearsStr ? "({$yearsStr})" : null,
                    'street' => ($block = $complex->blocks->first()) && $block->street
                        ? ($block->street->name . ' ' . ($block->building_number ?? ''))
                        : null,
                    'address' => implode(', ', array_filter([
                        $complex->district ? $complex->district->name : null,
                        $complex->city ? $complex->city->name : null,
                        ($complex->city && $complex->city->state) ? $complex->city->state->name : null,
                    ])),
                ],

                // Тип объекта
                'property_type' => [
                    'category' => $complex->housingClass ? $complex->housingClass->name : 'Жилье',
                    'types' => $complex->objects_count ? "{$complex->objects_count} объектов" : null,
                ],

                // Площадь
                'area' => [
                    'from' => $complex->area_from,
                    'to' => $complex->area_to,
                ],

                // Состояние
                'condition' => [
                    'conditions' => $conditionNames,
                    'wall_type' => implode(', ', $wallTypes) ?: null,
                ],

                // Этажность
                'floor' => $floorsStr,

                // Фото
                'photo' => $complex->photos && count($complex->photos) > 0
                    ? Storage::url($complex->photos[0])
                    : null,

                // Цена
                'price' => [
                    'total' => $complex->price_total
                        ? number_format($complex->price_total, 0, '', ' ') . ' ' . ($complex->currency ?? 'USD')
                        : null,
                    'per_m2' => $complex->price_per_m2
                        ? number_format($complex->price_per_m2, 0, '', ' ') . ' ' . ($complex->currency ?? 'USD')
                        : null,
                ],

                // Контакт
                'contact' => [
                    'has_contact' => $contact !== null,
                    'full_name' => $contact ? $contact->full_name : null,
                    'contact_type_name' => $contact ? $contact->contact_type_name : null,
                    'phone' => $contactPhone,
                ],

                // Действия
                'actions' => null,

                // Данные для child row
                'description' => $complex->description,
                'agent_notes' => $complex->agent_notes,
                'special_conditions' => $complex->special_conditions,
                'features' => $featureNames,
                'blocks' => $complex->blocks->map(function ($block) {
                    return [
                        'id' => $block->id,
                        'name' => $block->name,
                        'address' => $block->street
                            ? ($block->street->name . ' ' . ($block->building_number ?? ''))
                            : null,
                        'wall_type' => $block->wallType ? $block->wallType->name : null,
                        'heating_type' => $block->heatingType ? $block->heatingType->name : null,
                        'floors' => $block->floors_total,
                        'year_built' => $block->year_built,
                        'photo' => $block->plan_path ? Storage::url($block->plan_path) : null,
                    ];
                })->toArray(),
                'created_at' => $complex->created_at ? $complex->created_at->format('d.m.Y') : null,
                'updated_at' => $complex->updated_at ? $complex->updated_at->format('d.m.Y') : null,
            ];
        });

        // ========== Ответ в формате DataTables ==========
        return response()->json([
            'draw' => (int) $request->get('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
