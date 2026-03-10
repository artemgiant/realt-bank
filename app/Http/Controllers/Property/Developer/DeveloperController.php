<?php

namespace App\Http\Controllers\Property\Developer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Property\Developer\Actions\CreateDeveloper;
use App\Http\Controllers\Property\Developer\Actions\DeleteDeveloper;
use App\Http\Controllers\Property\Developer\Actions\UpdateDeveloper;
use App\Http\Controllers\Property\Developer\Presenters\DeveloperTablePresenter;
use App\Http\Controllers\Property\Developer\Queries\DeveloperIndexQuery;
use App\Http\Controllers\Property\Developer\Requests\StoreDeveloperRequest;
use App\Http\Controllers\Property\Developer\Requests\UpdateDeveloperRequest;
use App\Http\Controllers\Property\Developer\ViewData\DeveloperFormData;
use App\Models\Reference\Developer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер девелоперов — тонкий координатор.
 *
 * НЕ содержит бизнес-логику, НЕ строит запросы, НЕ форматирует данные.
 * Делегирует работу: Requests, Actions, Queries, Presenters, ViewData.
 */
class DeveloperController extends Controller
{
    /**
     * Список девелоперов — страница.
     * Сами девелоперы загружаются через AJAX (ajaxData).
     */
    public function index(Request $request): View
    {
        $developers = Developer::with(['contacts.phones'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.developers.index', [
            'developers' => $developers,
        ]);
    }

    /**
     * AJAX endpoint для DataTables Server-Side.
     * Делегирует фильтрацию -> DeveloperIndexQuery, форматирование -> DeveloperTablePresenter.
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $sortField = $request->input('sort_field', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $query = new DeveloperIndexQuery();
        $query->applyFilters($request)
              ->applySorting($sortField, $sortDir);

        $presenter = new DeveloperTablePresenter();

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $query->getTotal(),
            'recordsFiltered' => $query->getFiltered(),
            'data' => $presenter->toCollection($query->paginate($start, $length)),
        ]);
    }

    /**
     * Форма создания девелопера. Справочники из DeveloperFormData.
     */
    public function create(): View
    {
        return view('pages.developers.create', DeveloperFormData::get());
    }

    /**
     * Создание девелопера. Валидация -> StoreDeveloperRequest, логика -> CreateDeveloper.
     */
    public function store(StoreDeveloperRequest $request, CreateDeveloper $action): RedirectResponse
    {
        try {
            $action->execute($request->validated(), $request);

            return redirect()
                ->route('developers.index')
                ->with('success', 'Девелопер успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании девелопера: ' . $e->getMessage());
        }
    }

    /**
     * Просмотр девелопера с загруженными связями.
     */
    public function show(Developer $developer): View
    {
        $developer->load(['contacts.phones', 'complexes']);

        return view('pages.developers.show', [
            'developer' => $developer,
        ]);
    }

    /**
     * Форма редактирования девелопера. Подгружает связи + справочники из DeveloperFormData.
     */
    public function edit(Developer $developer): View
    {
        $developer->load(['contacts.phones']);

        return view('pages.developers.edit', [
            'developer' => $developer,
            ...DeveloperFormData::get(),
        ]);
    }

    /**
     * Обновление девелопера. Валидация -> UpdateDeveloperRequest, логика -> UpdateDeveloper.
     */
    public function update(UpdateDeveloperRequest $request, Developer $developer, UpdateDeveloper $action): RedirectResponse
    {
        try {
            $action->execute($developer, $request->validated(), $request);

            return redirect()
                ->route('developers.index')
                ->with('success', 'Девелопер успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении девелопера: ' . $e->getMessage());
        }
    }

    /**
     * Удаление девелопера. Логика -> DeleteDeveloper.
     */
    public function destroy(Developer $developer, DeleteDeveloper $action): RedirectResponse
    {
        try {
            $action->execute($developer);

            return redirect()
                ->route('developers.index')
                ->with('success', 'Девелопер успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении девелопера: ' . $e->getMessage());
        }
    }

    /**
     * AJAX поиск девелоперов для Select2.
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
}
