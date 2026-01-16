<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Contact\Contact;
use App\Models\Reference\Developer;
use App\Models\Reference\DeveloperLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DeveloperController extends Controller
{
    /**
     * Display a listing of developers.
     */
    public function index(Request $request): View
    {
        $developers = Developer::with(['contact.phones'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.developers.index', [
            'developers' => $developers,
        ]);
    }

    /**
     * Show the form for creating a new developer.
     */
    public function create(): View
    {
        return view('pages.developers.create', [
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),
        ]);
    }

    /**
     * Store a newly created developer.
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
            'year_founded' => 'nullable|integer|min:1900|max:' . date('Y'),
            'materials_url' => 'nullable|url|max:255',
            'agent_notes' => 'nullable|string|max:5000',

            // Контакт
            'contact_id' => 'nullable|exists:contacts,id',

            // Логотип
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'name_ua.max' => 'Название (UA) слишком длинное',
            'name_ru.max' => 'Название (RU) слишком длинное',
            'name_en.max' => 'Название (EN) слишком длинное',
            'year_founded.integer' => 'Год основания должен быть числом',
            'year_founded.min' => 'Год основания не может быть меньше 1900',
            'year_founded.max' => 'Год основания не может быть больше текущего года',
            'materials_url.url' => 'Введите корректную ссылку',
            'logo.image' => 'Файл должен быть изображением',
            'logo.mimes' => 'Разрешены только: JPEG, PNG, WebP',
            'logo.max' => 'Максимальный размер логотипа 2MB',
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

            // Создаем девелопера
            $developer = Developer::create([
                'name' => $mainName,
                'slug' => Str::slug($mainName),
                'contact_id' => $validated['contact_id'] ?? null,
                'website' => $validated['materials_url'] ?? null,
                'description' => $validated['description_ru'] ?? $validated['description_ua'] ?? $validated['description_en'] ?? null,
                'year_founded' => $validated['year_founded'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
                'is_active' => true,
            ]);

            // Сохраняем логотип
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store("developers/{$developer->id}/logo", 'public');
                $developer->update(['logo_path' => $logoPath]);
            }

            // Сохраняем локации
            $this->saveLocations($developer, $request->input('locations', []));

            DB::commit();

            return redirect()
                ->route('developers.index')
                ->with('success', 'Девелопер успешно создан!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании девелопера: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified developer.
     */
    public function show(Developer $developer): View
    {
        $developer->load(['contact.phones', 'complexes']);

        return view('pages.developers.show', [
            'developer' => $developer,
        ]);
    }

    /**
     * Show the form for editing the specified developer.
     */
    public function edit(Developer $developer): View
    {
        $developer->load(['contact.phones']);

        return view('pages.developers.edit', [
            'developer' => $developer,
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),
        ]);
    }

    /**
     * Update the specified developer.
     */
    public function update(Request $request, Developer $developer): RedirectResponse
    {
        $validated = $request->validate([
            'name_ua' => 'nullable|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ua' => 'nullable|string|max:10000',
            'description_ru' => 'nullable|string|max:10000',
            'description_en' => 'nullable|string|max:10000',
            'year_founded' => 'nullable|integer|min:1900|max:' . date('Y'),
            'materials_url' => 'nullable|url|max:255',
            'agent_notes' => 'nullable|string|max:5000',
            'contact_id' => 'nullable|exists:contacts,id',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'name_ua.max' => 'Название (UA) слишком длинное',
            'name_ru.max' => 'Название (RU) слишком длинное',
            'name_en.max' => 'Название (EN) слишком длинное',
            'year_founded.integer' => 'Год основания должен быть числом',
            'year_founded.min' => 'Год основания не может быть меньше 1900',
            'year_founded.max' => 'Год основания не может быть больше текущего года',
            'materials_url.url' => 'Введите корректную ссылку',
            'logo.image' => 'Файл должен быть изображением',
            'logo.mimes' => 'Разрешены только: JPEG, PNG, WebP',
            'logo.max' => 'Максимальный размер логотипа 2MB',
        ]);

        try {
            DB::beginTransaction();

            $mainName = $validated['name_ru'] ?? $validated['name_ua'] ?? $validated['name_en'] ?? $developer->name;

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

            $developer->update([
                'name' => $mainName,
                'slug' => Str::slug($mainName),
                'contact_id' => $validated['contact_id'] ?? null,
                'website' => $validated['materials_url'] ?? null,
                'description' => $validated['description_ru'] ?? $validated['description_ua'] ?? $validated['description_en'] ?? null,
                'year_founded' => $validated['year_founded'] ?? null,
                'agent_notes' => $validated['agent_notes'] ?? null,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
            ]);

            // Обновляем логотип
            if ($request->hasFile('logo')) {
                // Удаляем старый логотип
                if ($developer->logo_path) {
                    Storage::disk('public')->delete($developer->logo_path);
                }

                $logoPath = $request->file('logo')->store("developers/{$developer->id}/logo", 'public');
                $developer->update(['logo_path' => $logoPath]);
            }

            // Обновляем локации
            $this->saveLocations($developer, $request->input('locations', []));

            DB::commit();

            return redirect()
                ->route('developers.index')
                ->with('success', 'Девелопер успешно обновлен!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении девелопера: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified developer.
     */
    public function destroy(Developer $developer): RedirectResponse
    {
        try {
            // Удаляем логотип
            if ($developer->logo_path) {
                Storage::disk('public')->delete($developer->logo_path);
            }

            $developer->delete();

            return redirect()
                ->route('developers.index')
                ->with('success', 'Девелопер успешно удален!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении девелопера: ' . $e->getMessage());
        }
    }

    /**
     * AJAX данные для DataTables
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $query = Developer::with(['contact.phones', 'complexes', 'locations']);

        // Фильтр по ID
        if ($request->filled('search_id')) {
            $query->where('id', $request->input('search_id'));
        }

        // Фильтр по контакту (имя, телефон)
        if ($request->filled('contact_search')) {
            $search = $request->input('contact_search');
            $query->whereHas('contact', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhereHas('phones', function ($pq) use ($search) {
                        $pq->where('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Проверяем, выбраны ли конкретные города
        $selectedCityIds = [];
        if ($request->filled('city_ids')) {
            $decoded = json_decode($request->input('city_ids'), true);
            if (is_array($decoded) && count($decoded) > 0) {
                $selectedCityIds = $decoded;
            }
        }

        // Если выбраны конкретные города - фильтруем только по ним
        if (!empty($selectedCityIds)) {
            $query->whereHas('locations', function ($q) use ($selectedCityIds) {
                $q->where('location_type', 'city')
                    ->whereIn('location_id', $selectedCityIds);
            });
        }
        // Иначе фильтруем по локации (страна/регион)
        elseif ($request->filled('location_type') && $request->filled('location_id')) {
            $locationType = $request->input('location_type');
            // Маппинг 'region' -> 'state' (JS отправляет 'region', в БД хранится 'state')
            if ($locationType === 'region') {
                $locationType = 'state';
            }
            $locationId = (int) $request->input('location_id');

            $query->whereHas('locations', function ($q) use ($locationType, $locationId) {
                if ($locationType === 'country') {
                    // Для страны: ищем девелоперов с этой страной, или с областями/городами в этой стране
                    $stateIds = \App\Models\Location\State::where('country_id', $locationId)->pluck('id')->toArray();
                    $cityIds = \App\Models\Location\City::whereIn('state_id', $stateIds)->pluck('id')->toArray();

                    $q->where(function ($subQ) use ($locationId, $stateIds, $cityIds) {
                        $subQ->where(function ($w) use ($locationId) {
                            $w->where('location_type', 'country')->where('location_id', $locationId);
                        });
                        if (!empty($stateIds)) {
                            $subQ->orWhere(function ($w) use ($stateIds) {
                                $w->where('location_type', 'state')->whereIn('location_id', $stateIds);
                            });
                        }
                        if (!empty($cityIds)) {
                            $subQ->orWhere(function ($w) use ($cityIds) {
                                $w->where('location_type', 'city')->whereIn('location_id', $cityIds);
                            });
                        }
                    });
                } elseif ($locationType === 'state') {
                    // Для области: ищем девелоперов с этой областью или с городами в этой области
                    $cityIds = \App\Models\Location\City::where('state_id', $locationId)->pluck('id')->toArray();

                    $q->where(function ($subQ) use ($locationId, $cityIds) {
                        $subQ->where(function ($w) use ($locationId) {
                            $w->where('location_type', 'state')->where('location_id', $locationId);
                        });
                        if (!empty($cityIds)) {
                            $subQ->orWhere(function ($w) use ($cityIds) {
                                $w->where('location_type', 'city')->whereIn('location_id', $cityIds);
                            });
                        }
                    });
                } else {
                    // Для города: точное совпадение
                    $q->where('location_type', $locationType)
                        ->where('location_id', $locationId);
                }
            });
        }

        // Сортировка
        $sortField = $request->input('sort_field', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $allowedSortFields = ['created_at', 'name', 'year_founded'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Подсчет общего количества
        $totalRecords = Developer::count();
        $filteredRecords = $query->count();

        // Пагинация DataTables
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $developers = $query->skip($start)->take($length)->get();

        // Форматирование данных для DataTables
        $data = $developers->map(function ($developer) {
            // Локация из первой записи locations
            $location = $developer->locations->first();
            $locationText = $location ? $location->full_location_name : '-';

            // Контакт
            $contact = $developer->contact;
            $contactData = [
                'has_contact' => (bool) $contact,
                'full_name' => $contact ? $contact->full_name : null,
                'contact_type_name' => $contact ? $contact->contact_type_name : null,
                'phone' => $contact ? $contact->primary_phone : null,
            ];

            return [
                'id' => $developer->id,
                'checkbox' => $developer->id,
                'developer' => [
                    'id' => $developer->id,
                    'name' => $developer->name,
                    'logo_url' => $developer->logo_url,
                    'location' => $locationText,
                ],
                'year_founded' => $developer->year_founded ?? '-',
                'complexes_count' => $developer->complexes->count(),
                'contact' => $contactData,
                'actions' => $developer->id,
                // Дополнительные данные для child row
                'description' => $developer->description,
                'website' => $developer->website,
                'agent_notes' => $developer->agent_notes,
                'created_at_formatted' => $developer->created_at?->format('d.m.Y H:i'),
                'updated_at_formatted' => $developer->updated_at?->format('d.m.Y H:i'),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * AJAX поиск девелоперов (для Select2)
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        $search = $request->input('q', '');

        $developers = Developer::active()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json([
            'results' => $developers->map(function ($developer) {
                return [
                    'id' => $developer->id,
                    'text' => $developer->name,
                ];
            }),
        ]);
    }

    /**
     * Сохранение локаций девелопера
     */
    private function saveLocations(Developer $developer, array $locations): void
    {
        // Удаляем старые локации
        $developer->locations()->delete();

        // Добавляем новые
        foreach ($locations as $locationData) {
            $locationValue = $locationData['location'] ?? null;

            if (empty($locationValue)) {
                continue;
            }

            // Парсим значение формата "type:id" (например "city:123")
            $parts = explode(':', $locationValue);
            if (count($parts) !== 2) {
                continue;
            }

            $locationType = $parts[0];
            $locationId = (int) $parts[1];

            // Валидация типа
            if (!in_array($locationType, ['country', 'state', 'city'])) {
                continue;
            }

            // Получаем название локации
            $locationName = '';
            $fullLocationName = '';

            switch ($locationType) {
                case 'country':
                    $location = \App\Models\Location\Country::find($locationId);
                    if ($location) {
                        $locationName = $location->name;
                        $fullLocationName = $location->name;
                    }
                    break;

                case 'state':
                    $location = \App\Models\Location\State::with('country')->find($locationId);
                    if ($location) {
                        $locationName = $location->name;
                        $fullLocationName = $location->name;
                        if ($location->country) {
                            $fullLocationName = $location->country->name . ', ' . $location->name;
                        }
                    }
                    break;

                case 'city':
                    $location = \App\Models\Location\City::with(['state.country'])->find($locationId);
                    if ($location) {
                        $locationName = $location->name;
                        $fullLocationName = $location->name;
                        if ($location->state) {
                            $fullLocationName = $location->state->name . ', ' . $location->name;
                            if ($location->state->country) {
                                $fullLocationName = $location->state->country->name . ', ' . $fullLocationName;
                            }
                        }
                    }
                    break;
            }

            if (!empty($locationName)) {
                DeveloperLocation::create([
                    'developer_id' => $developer->id,
                    'location_type' => $locationType,
                    'location_id' => $locationId,
                    'location_name' => $locationName,
                    'full_location_name' => $fullLocationName,
                ]);
            }
        }
    }
}
