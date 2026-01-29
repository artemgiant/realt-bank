<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Contact\Contact;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index(): View
    {
        return view('pages.companies.index');
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View
    {
        return view('pages.companies.create', [
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),
        ]);
    }

    /**
     * Store a newly created company.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Определяем основное название (приоритет: RU -> UA -> EN)
            $mainName = $validated['name_ru']
                ?? $validated['name_ua']
                ?? $validated['name_en']
                ?? 'Без названия';

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

            // Создаем компанию
            $company = Company::create([
                'name' => $mainName,
                'slug' => Str::slug($mainName) . '-' . uniqid(),
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
                'country_id' => $validated['country_id'] ?? null,
                'state_id' => $validated['state_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'zone_id' => $validated['zone_id'] ?? null,
                'street_id' => $validated['street_id'] ?? null,
                'building_number' => $validated['building_number'] ?? null,
                'office_number' => $validated['office_number'] ?? null,
                'website' => $validated['website'] ?? null,
                'edrpou_code' => $validated['edrpou_code'] ?? null,
                'company_type' => $validated['company_type'] ?? null,
                'is_active' => true,
            ]);

            // Привязываем контакты через полиморфную связь
            if (!empty($validated['contact_ids'])) {
                $contactData = [];
                foreach ($validated['contact_ids'] as $index => $id) {
                    $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
                }
                $company->contacts()->attach($contactData);
            }

            // Сохраняем логотип
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store("companies/{$company->id}/logo", 'public');
                $company->update(['logo_path' => $logoPath]);
            }

            // Сохраняем офисы
            $this->saveOffices($company, $validated['offices'] ?? []);

            DB::commit();

            return redirect()
                ->route('companies.index')
                ->with('success', 'Компания успешно создана!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании компании: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): View
    {
        $company->load(['contacts.phones', 'offices.contacts.phones', 'city', 'state', 'street']);

        return view('pages.companies.show', [
            'company' => $company,
        ]);
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): View
    {
        $company->load(['contacts.phones', 'offices.contacts.phones']);

        return view('pages.companies.edit', [
            'company' => $company,
            'contacts' => Contact::with('phones')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(100)
                ->get(),
        ]);
    }

    /**
     * Update the specified company.
     */
    public function update(StoreCompanyRequest $request, Company $company): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $mainName = $validated['name_ru']
                ?? $validated['name_ua']
                ?? $validated['name_en']
                ?? $company->name;

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

            $company->update([
                'name' => $mainName,
                'slug' => Str::slug($mainName) . '-' . $company->id,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
                'country_id' => $validated['country_id'] ?? null,
                'state_id' => $validated['state_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'zone_id' => $validated['zone_id'] ?? null,
                'street_id' => $validated['street_id'] ?? null,
                'building_number' => $validated['building_number'] ?? null,
                'office_number' => $validated['office_number'] ?? null,
                'website' => $validated['website'] ?? null,
                'edrpou_code' => $validated['edrpou_code'] ?? null,
                'company_type' => $validated['company_type'] ?? null,
            ]);

            // Обновляем контакты через полиморфную связь
            if (isset($validated['contact_ids'])) {
                if (!empty($validated['contact_ids'])) {
                    $contactData = [];
                    foreach ($validated['contact_ids'] as $index => $id) {
                        $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
                    }
                    $company->contacts()->sync($contactData);
                } else {
                    $company->contacts()->detach();
                }
            }

            // Обновляем логотип
            if ($request->hasFile('logo')) {
                if ($company->logo_path) {
                    Storage::disk('public')->delete($company->logo_path);
                }
                $logoPath = $request->file('logo')->store("companies/{$company->id}/logo", 'public');
                $company->update(['logo_path' => $logoPath]);
            }

            // Обновляем офисы
            $this->saveOffices($company, $validated['offices'] ?? []);

            DB::commit();

            return redirect()
                ->route('companies.index')
                ->with('success', 'Компания успешно обновлена!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении компании: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified company.
     */
    public function destroy(Company $company): RedirectResponse
    {
        try {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }

            // Офисы удалятся каскадно благодаря FK
            $company->delete();

            return redirect()
                ->route('companies.index')
                ->with('success', 'Компания успешно удалена!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении компании: ' . $e->getMessage());
        }
    }

    /**
     * AJAX данные для DataTables
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $query = Company::with(['contacts.phones', 'offices', 'city', 'state']);

        // Фильтр по ID
        if ($request->filled('search_id')) {
            $query->where('id', $request->input('search_id'));
        }

        // Фильтр по названию
        if ($request->filled('search_name')) {
            $search = $request->input('search_name');
            $query->where('name', 'like', "%{$search}%");
        }

        // Фильтр по типу компании
        if ($request->filled('company_type')) {
            $query->where('company_type', $request->input('company_type'));
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Фильтр по городу
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        // Сортировка
        $sortField = $request->input('sort_field', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $allowedSortFields = ['created_at', 'name'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Подсчет общего количества
        $totalRecords = Company::count();
        $filteredRecords = $query->count();

        // Пагинация DataTables
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $companies = $query->skip($start)->take($length)->get();

        // Форматирование данных для DataTables
        $data = $companies->map(function ($company) {
            $contact = $company->primary_contact;

            return [
                'id' => $company->id,
                'checkbox' => $company->id,
                'logo_url' => $company->logo_url,
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'address' => $company->full_address,
                ],
                'director' => [
                    'has_contact' => (bool) $contact,
                    'full_name' => $contact?->full_name,
                    'phone' => $contact?->primary_phone,
                ],
                'offices_count' => $company->offices->count(),
                'team_count' => 0,
                'properties_count' => 0,
                'commission' => '-',
                'is_active' => $company->is_active,
                'actions' => $company->id,
                'website' => $company->website,
                'description' => $company->description,
                'created_at_formatted' => $company->created_at?->format('d.m.Y H:i'),
                'updated_at_formatted' => $company->updated_at?->format('d.m.Y H:i'),
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
     * AJAX поиск компаний (для Select2)
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        $search = $request->input('q', '');

        $companies = Company::active()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json([
            'results' => $companies->map(function ($company) {
                return [
                    'id' => $company->id,
                    'text' => $company->name,
                ];
            }),
        ]);
    }

    /**
     * Сохранение офисов компании
     */
    private function saveOffices(Company $company, array $offices): void
    {
        // Удаляем старые офисы
        foreach ($company->offices as $office) {
            $office->contacts()->detach();
            $office->delete();
        }

        // Добавляем новые офисы
        foreach ($offices as $index => $officeData) {
            if (empty($officeData['name'])) {
                continue;
            }

            $office = CompanyOffice::create([
                'company_id' => $company->id,
                'name' => $officeData['name'],
                'country_id' => $officeData['country_id'] ?? null,
                'state_id' => $officeData['state_id'] ?? null,
                'city_id' => $officeData['city_id'] ?? null,
                'district_id' => $officeData['district_id'] ?? null,
                'zone_id' => $officeData['zone_id'] ?? null,
                'street_id' => $officeData['street_id'] ?? null,
                'building_number' => $officeData['building_number'] ?? null,
                'office_number' => $officeData['office_number'] ?? null,
                'sort_order' => $index,
                'is_active' => true,
            ]);

            // Кешируем полный адрес
            $office->load(['street', 'city']);
            $office->update(['full_address' => $office->full_address_computed]);

            // Привязываем контакты офиса
            if (!empty($officeData['contact_ids'])) {
                $contactData = [];
                foreach ($officeData['contact_ids'] as $idx => $id) {
                    $contactData[$id] = ['role' => ($idx === 0 ? 'primary' : 'secondary')];
                }
                $office->contacts()->attach($contactData);
            }
        }
    }
}
