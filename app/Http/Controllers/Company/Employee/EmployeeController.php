<?php

namespace App\Http\Controllers\Company\Employee;

use App\Http\Controllers\Company\Employee\Presenters\EmployeeTablePresenter;
use App\Http\Controllers\Company\Employee\Queries\EmployeeIndexQuery;
use App\Http\Controllers\Company\Employee\Requests\StoreEmployeeRequest;
use App\Http\Controllers\Company\Employee\Requests\UpdateEmployeeRequest;
use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use App\Models\Reference\Dictionary;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\PhoneFormatter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Контроллер сотрудников — тонкий координатор.
 *
 * НЕ содержит бизнес-логику фильтрации и форматирования.
 * Делегирует работу: Requests, Queries, Presenters.
 */
class EmployeeController extends Controller
{
    /**
     * Список сотрудников (страница).
     * Сами сотрудники загружаются через AJAX (ajaxData).
     */
    public function index()
    {
        $positions = Dictionary::getEmployeePositions();
        $statuses = Dictionary::getEmployeeStatuses();
        $companies = Company::active()->orderBy('name')->get();
        $offices = CompanyOffice::active()->orderBy('name')->get();
        $tags = Dictionary::getAgentTags();

        return view('pages.employees.index', compact(
            'positions',
            'statuses',
            'companies',
            'offices',
            'tags'
        ));
    }

    /**
     * AJAX endpoint для DataTables Server-Side.
     * Делегирует фильтрацию → EmployeeIndexQuery, форматирование → EmployeeTablePresenter.
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $sortColumn = $request->input('sort_column', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $query = new EmployeeIndexQuery();
        $query->applyFilters($request)
              ->applySorting($sortColumn, $sortDirection);

        // Справочники для Select2 в строках таблицы
        $positions = Dictionary::getEmployeePositions();
        $offices = CompanyOffice::active()->get();

        $presenter = new EmployeeTablePresenter($positions, $offices);

        return response()->json([
            'draw'            => $request->input('draw'),
            'recordsTotal'    => $query->getTotal(),
            'recordsFiltered' => $query->getFiltered(),
            'data'            => $presenter->toCollection($query->paginate($start, $length)),
        ]);
    }

    /**
     * Создание сотрудника (AJAX).
     * Валидация через StoreEmployeeRequest.
     * Создаёт User + Employee, загружает фото.
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Форматирование телефона
        $validated['phone'] = PhoneFormatter::format($validated['phone']);

        // Создание пользователя
        $user = User::create([
            'name'     => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'password' => $validated['password'], // автоматически хешируется благодаря cast
        ]);

        // Загрузка фото
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('employees', 'public');
        }

        // Привязка user_id к сотруднику
        $validated['user_id'] = $user->id;

        // Удаляем password из validated, так как его нет в fillable Employee
        unset($validated['password']);

        $employee = Employee::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Сотрудник создан',
            'employee' => $employee,
        ]);
    }

    /**
     * Получение данных сотрудника (AJAX).
     * Загружает связи и возвращает полные данные для модального окна.
     */
    public function show(Employee $employee): JsonResponse
    {
        $employee->load(['company', 'office', 'position', 'status']);

        return response()->json([
            'success'  => true,
            'employee' => [
                'id'            => $employee->id,
                'user_id'       => $employee->user_id,
                'first_name'    => $employee->first_name,
                'last_name'     => $employee->last_name,
                'middle_name'   => $employee->middle_name,
                'full_name'     => $employee->full_name,
                'email'         => $employee->email,
                'phone'         => $employee->phone,
                'birthday'      => $employee->birthday?->format('Y-m-d'),
                'company_id'    => $employee->company_id,
                'company_name'  => $employee->company?->name,
                'office_id'     => $employee->office_id,
                'office_name'   => $employee->office?->name,
                'position_id'   => $employee->position_id,
                'position_name' => $employee->position?->name,
                'status_id'     => $employee->status_id,
                'status_name'   => $employee->status?->name,
                'tag_ids'       => $employee->tag_ids,
                'passport'      => $employee->passport,
                'inn'           => $employee->inn,
                'comment'       => $employee->comment,
                'photo_url'     => $employee->photo_url,
                'active_until'  => $employee->active_until?->format('Y-m-d H:i'),
                'is_active'     => $employee->is_active,
            ],
        ]);
    }

    /**
     * Обновление сотрудника (AJAX).
     * Валидация через UpdateEmployeeRequest.
     * Обновляет данные, заменяет фото при загрузке нового.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $validated = $request->validated();

        // Загрузка нового фото
        if ($request->hasFile('photo')) {
            // Удаление старого фото
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('employees', 'public');
        }

        // Форматирование телефона
        if (!empty($validated['phone'])) {
            $validated['phone'] = PhoneFormatter::format($validated['phone']);
        }

        // Синхронизация данных пользователя
        if ($employee->user) {
            $userData = [
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            ];
            if (!empty($validated['email'])) {
                $userData['email'] = $validated['email'];
            }
            if (!empty($validated['phone'])) {
                $userData['phone'] = $validated['phone'];
            }
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $employee->user->update($userData);
        }
        unset($validated['password']);

        $employee->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Сотрудник обновлен',
            'employee' => $employee,
        ]);
    }

    /**
     * Удаление сотрудника (AJAX).
     * Удаляет фото из хранилища и запись из БД.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        // Удаление фото
        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Сотрудник удален',
        ]);
    }

    /**
     * Обновление должности сотрудника (AJAX из таблицы).
     * Используется для inline-редактирования Select2 в DataTables.
     */
    public function updatePosition(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'position_id' => 'nullable|exists:dictionaries,id',
        ]);

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Должность обновлена',
        ]);
    }

    /**
     * Обновление офиса сотрудника (AJAX из таблицы).
     * Используется для inline-редактирования Select2 в DataTables.
     */
    public function updateOffice(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'office_id' => 'nullable|exists:company_offices,id',
        ]);

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Офис обновлен',
        ]);
    }

    /**
     * Поиск сотрудников для Select2 (AJAX).
     * Фильтрует по тексту, офису и компании, возвращает id + text.
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        $search = $request->input('q', '');

        $query = Employee::query()
            ->active()
            ->when($search, fn($q) => $q->search($search));

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $employees = $query->limit(20)
            ->get(['id', 'first_name', 'last_name', 'phone']);

        $results = $employees->map(fn($e) => [
            'id'   => $e->id,
            'text' => $e->full_name . ($e->phone ? " ({$e->phone})" : ''),
        ]);

        return response()->json(['results' => $results]);
    }
}
