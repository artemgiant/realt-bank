<?php

namespace App\Http\Controllers\Company\Company;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Company\Company\Actions\CreateCompany;
use App\Http\Controllers\Company\Company\Actions\DeleteCompany;
use App\Http\Controllers\Company\Company\Actions\UpdateCompany;
use App\Http\Controllers\Company\Company\Presenters\CompanyTablePresenter;
use App\Http\Controllers\Company\Company\Queries\CompanyIndexQuery;
use App\Http\Controllers\Company\Company\ViewData\CompanyFormData;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Reference\Company;
use App\Models\Reference\Dictionary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер компаний — тонкий координатор.
 *
 * НЕ содержит бизнес-логику, НЕ строит запросы, НЕ форматирует данные.
 * Делегирует работу: Requests, Actions, Queries, Presenters, ViewData.
 */
class CompanyController extends Controller
{
    /**
     * Список компаний — страница с фильтрами.
     * Сами компании загружаются через AJAX (ajaxData).
     */
    public function index(): View
    {
        return view('pages.companies.index', [
            'agencyTypes' => Dictionary::getAgencyTypes(),
        ]);
    }

    /**
     * Форма создания компании. Справочники из CompanyFormData.
     */
    public function create(): View
    {
        return view('pages.companies.create', CompanyFormData::get());
    }

    /**
     * Создание компании. Валидация → StoreCompanyRequest, логика → CreateCompany.
     */
    public function store(StoreCompanyRequest $request, CreateCompany $action): RedirectResponse
    {
        try {
            $action->execute($request->validated(), $request);

            return redirect()
                ->route('companies.index')
                ->with('success', 'Компания успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании компании: ' . $e->getMessage());
        }
    }

    /**
     * Просмотр компании с загруженными связями.
     */
    public function show(Company $company): View
    {
        $company->load(['contacts.phones', 'offices.contacts.phones', 'city', 'state', 'street']);

        return view('pages.companies.show', [
            'company' => $company,
        ]);
    }

    /**
     * Форма редактирования компании. Подгружает связи + справочники из CompanyFormData.
     */
    public function edit(Company $company): View
    {
        $company->load(['contacts.phones', 'offices.contacts.phones', 'state', 'city', 'street']);

        return view('pages.companies.edit', [
            'company' => $company,
            ...CompanyFormData::get(),
        ]);
    }

    /**
     * Обновление компании. Валидация → StoreCompanyRequest, логика → UpdateCompany.
     */
    public function update(StoreCompanyRequest $request, Company $company, UpdateCompany $action): RedirectResponse
    {
        try {
            $action->execute($company, $request->validated(), $request);

            return redirect()
                ->route('companies.index')
                ->with('success', 'Компания успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении компании: ' . $e->getMessage());
        }
    }

    /**
     * Удаление компании. Логика → DeleteCompany.
     */
    public function destroy(Company $company, DeleteCompany $action): RedirectResponse
    {
        try {
            $action->execute($company);

            return redirect()
                ->route('companies.index')
                ->with('success', 'Компания успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении компании: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint для DataTables Server-Side.
     * Делегирует фильтрацию → CompanyIndexQuery, форматирование → CompanyTablePresenter.
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $sortField = $request->input('sort_field', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $query = new CompanyIndexQuery();
        $query->applyFilters($request)
              ->applySorting($sortField, $sortDir);

        $presenter = new CompanyTablePresenter();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $query->getTotal(),
            'recordsFiltered' => $query->getFiltered(),
            'data' => $presenter->toCollection($query->paginate($start, $length)),
        ]);
    }

    /**
     * AJAX получение офисов компании (для вложенной таблицы).
     */
    public function getOffices(Company $company): JsonResponse
    {
        $offices = $company->offices()->with(['contacts.phones', 'city', 'state', 'street'])->get();

        $data = $offices->map(function ($office) {
            $contact = $office->contacts->first();

            return [
                'id' => $office->id,
                'name' => $office->name,
                'logo' => null,
                'address' => $office->building_number
                    ? ($office->street ? $office->street->name . ', ' : '') . $office->building_number . ($office->office_number ? ', оф. ' . $office->office_number : '')
                    : ($office->full_address ?? '-'),
                'location' => implode(', ', array_filter([
                    $office->city ? $office->city->name : null,
                    $office->state ? $office->state->name : null,
                ])),
                'responsible' => $contact ? [
                    'name' => $contact->full_name,
                    'position' => $contact->pivot->role === 'primary' ? 'Ответственный' : 'Контакт',
                    'phone' => $contact->primary_phone,
                ] : null,
                'team_count' => 0,
                'properties_count' => 0,
                'deals_count' => 0,
                'success_deals_count' => 0,
                'failed_deals_count' => 0,
                'commission_from' => null,
                'commission_to' => null,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * AJAX поиск компаний (для Select2 dropdown).
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
}
