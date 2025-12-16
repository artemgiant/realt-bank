<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
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
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display a listing of the properties.
     */
    public function index(): View
    {
        return view('pages.properties.index');
    }

    /**
     * Show the form for creating a new property.
     */
    public function create(): View
    {
        return view('pages.properties.create', [
            // Валюти
            'currencies' => Currency::active()->get(),

            // Джерела
            'sources' => Source::active()->orderBy('name')->get(),

            // Комплекси
            'complexes' => Complex::active()->orderBy('name')->get(),

            // Контакти (для модального вікна)
            'contacts' => Contact::orderBy('name')->limit(100)->get(),

            // Країни
            'countries' => Country::active()->orderBy('name')->get(),

            // Довідники
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

            // Роки побудови (від поточного до 1950)
            'years' => range(date('Y') + 5, 1950),
        ]);
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request): RedirectResponse
    {

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // ========== Створюємо основний запис ==========
            $property = Property::create([
                'user_id' => auth()->id(),

                // Зв'язки
                'contact_id' => $validated['contact_id'] ?? null,
                'source_id' => $validated['source_id'] ?? null,
                'currency_id' => $validated['currency_id'],

                // Комплекс
                'complex_id' => $validated['complex_id'] ?? null,
                'section_id' => $validated['section_id'] ?? null,

                // Локація
                'country_id' => $validated['country_id'] ?? null,
                'region_id' => $validated['region_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'zone_id' => $validated['zone_id'] ?? null,
                'street_id' => $validated['street_id'] ?? null,
                'landmark_id' => $validated['landmark_id'] ?? null,
                'building_number' => $validated['building_number'] ?? null,
                'apartment_number' => $validated['apartment_number'] ?? null,
                'location_name' => $validated['location_name'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,

                // Довідники
                'deal_type_id' => $validated['deal_type_id'],
                'deal_kind_id' => $validated['deal_kind_id'] ?? null,
                'building_type_id' => $validated['building_type_id'] ?? null,
                'property_type_id' => $validated['property_type_id'] ?? null,
                'condition_id' => $validated['condition_id'] ?? null,
                'wall_type_id' => $validated['wall_type_id'] ?? null,
                'heating_type_id' => $validated['heating_type_id'] ?? null,
                'room_count_id' => $validated['room_count_id'] ?? null,
                'bathroom_count_id' => $validated['bathroom_count_id'] ?? null,
                'ceiling_height_id' => $validated['ceiling_height_id'] ?? null,

                // Характеристики
                'area_total' => $validated['area_total'] ?? null,
                'area_living' => $validated['area_living'] ?? null,
                'area_kitchen' => $validated['area_kitchen'] ?? null,
                'area_land' => $validated['area_land'] ?? null,
                'floor' => $validated['floor'] ?? null,
                'floors_total' => $validated['floors_total'] ?? null,
                'year_built' => $validated['year_built'] ?? null,

                // Ціна
                'price' => $validated['price'] ?? null,
                'commission' => $validated['commission'] ?? null,
                'commission_type' => $validated['commission_type'] ?? 'percent',

                // Медіа
                'youtube_url' => $validated['youtube_url'] ?? null,
                'external_url' => $validated['external_url'] ?? null,

                // Налаштування
                'is_visible_to_agents' => $validated['is_visible_to_agents'] ?? false,
                'notes' => $validated['notes'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
                'status' => $validated['status'] ?? 'draft',
            ]);

            // ========== Зберігаємо переклади ==========
            $this->saveTranslations($property, $validated);

            // ========== Зберігаємо особливості ==========
            if (!empty($validated['features'])) {
                $property->features()->sync($validated['features']);
            }

            // ========== Зберігаємо фото ==========
            if ($request->hasFile('photos')) {
                $this->savePhotos($property, $request->file('photos'));
            }

            // ========== Зберігаємо документи ==========
            if ($request->hasFile('documents')) {
                $this->saveDocuments($property, $request->file('documents'));
            }

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Об\'єкт успішно створено!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Помилка при створенні об\'єкта: ' . $e->getMessage());
        }
    }

    /**
     * Save property translations.
     */
    protected function saveTranslations(Property $property, array $validated): void
    {
        $locales = ['ua', 'ru', 'en'];

        foreach ($locales as $locale) {
            $title = $validated["title_{$locale}"] ?? null;
            $description = $validated["description_{$locale}"] ?? null;

            // Зберігаємо тільки якщо є хоча б заголовок або опис
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

            // Генеруємо унікальне ім'я файлу
            $filename = $photo->getClientOriginalName();
            $path = $photo->store("properties/{$property->id}/photos", 'public');

            PropertyPhoto::create([
                'property_id' => $property->id,
                'path' => $path,
                'filename' => $filename,
                'sort_order' => $sortOrder,
                'is_main' => $sortOrder === 1, // Перше фото - головне
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

            // Ті самі дані що і в create
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
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Оновлюємо основні дані
            $property->update([
                'contact_id' => $validated['contact_id'] ?? null,
                'source_id' => $validated['source_id'] ?? null,
                'currency_id' => $validated['currency_id'],
                'complex_id' => $validated['complex_id'] ?? null,
                'section_id' => $validated['section_id'] ?? null,
                'country_id' => $validated['country_id'] ?? null,
                'region_id' => $validated['region_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'zone_id' => $validated['zone_id'] ?? null,
                'street_id' => $validated['street_id'] ?? null,
                'landmark_id' => $validated['landmark_id'] ?? null,
                'building_number' => $validated['building_number'] ?? null,
                'apartment_number' => $validated['apartment_number'] ?? null,
                'location_name' => $validated['location_name'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'deal_type_id' => $validated['deal_type_id'],
                'deal_kind_id' => $validated['deal_kind_id'] ?? null,
                'building_type_id' => $validated['building_type_id'] ?? null,
                'property_type_id' => $validated['property_type_id'] ?? null,
                'condition_id' => $validated['condition_id'] ?? null,
                'wall_type_id' => $validated['wall_type_id'] ?? null,
                'heating_type_id' => $validated['heating_type_id'] ?? null,
                'room_count_id' => $validated['room_count_id'] ?? null,
                'bathroom_count_id' => $validated['bathroom_count_id'] ?? null,
                'ceiling_height_id' => $validated['ceiling_height_id'] ?? null,
                'area_total' => $validated['area_total'] ?? null,
                'area_living' => $validated['area_living'] ?? null,
                'area_kitchen' => $validated['area_kitchen'] ?? null,
                'area_land' => $validated['area_land'] ?? null,
                'floor' => $validated['floor'] ?? null,
                'floors_total' => $validated['floors_total'] ?? null,
                'year_built' => $validated['year_built'] ?? null,
                'price' => $validated['price'] ?? null,
                'commission' => $validated['commission'] ?? null,
                'commission_type' => $validated['commission_type'] ?? 'percent',
                'youtube_url' => $validated['youtube_url'] ?? null,
                'external_url' => $validated['external_url'] ?? null,
                'is_visible_to_agents' => $validated['is_visible_to_agents'] ?? false,
                'notes' => $validated['notes'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
                'status' => $validated['status'] ?? $property->status,
            ]);

            // Оновлюємо переклади
            $this->saveTranslations($property, $validated);

            // Оновлюємо особливості
            $property->features()->sync($validated['features'] ?? []);

            // Додаємо нові фото
            if ($request->hasFile('photos')) {
                $this->savePhotos($property, $request->file('photos'));
            }

            // Додаємо нові документи
            if ($request->hasFile('documents')) {
                $this->saveDocuments($property, $request->file('documents'));
            }

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Об\'єкт успішно оновлено!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Помилка при оновленні об\'єкта: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Property $property): RedirectResponse
    {
        try {
            // Видаляємо файли фото
            foreach ($property->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }

            // Видаляємо файли документів
            foreach ($property->documents as $document) {
                Storage::disk('public')->delete($document->path);
            }

            // Видаляємо папку об'єкта
            Storage::disk('public')->deleteDirectory("properties/{$property->id}");

            // Soft delete
            $property->delete();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Об\'єкт успішно видалено!');

        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні об\'єкта: ' . $e->getMessage());
        }
    }
}
