<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use App\Models\Reference\Dictionary;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Список сотрудников (страница)
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
     * AJAX данные для DataTables
     */
    public function ajaxData(Request $request): JsonResponse
    {
        $query = Employee::with(['company', 'office', 'position', 'status']);

        // Поиск (DataTables отправляет search как массив или строку)
        $search = $request->input('search');
        if (is_array($search) && !empty($search['value'])) {
            $query->search($search['value']);
        } elseif (is_string($search) && !empty($search)) {
            $query->search($search);
        }

        // Фильтр по должности
        if ($positionId = $request->input('position_id')) {
            $query->byPosition($positionId);
        }

        // Фильтр по статусу
        if ($statusId = $request->input('status')) {
            $query->byStatus($statusId);
        }

        // Фильтр по компании
        if ($companyId = $request->input('company_id')) {
            $query->byCompany($companyId);
        }

        // Фильтр по офису
        if ($officeId = $request->input('office_id')) {
            $query->byOffice($officeId);
        }

        // Фильтр по тегам
        if ($tags = $request->input('tags')) {
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tag_ids', $tag);
                }
            });
        }

        // Общее количество
        $totalRecords = Employee::count();
        $filteredRecords = $query->count();

        // Пагинация
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $employees = $query
            ->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        // Справочники для Select2 в таблице
        $positions = Dictionary::getEmployeePositions();
        $offices = CompanyOffice::active()->get();

        // Форматирование данных
        $data = $employees->map(function ($employee) use ($positions, $offices) {
            return [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'photo_url' => $employee->photo_url,
                'company_name' => $employee->company?->name,
                'phone' => $employee->phone,
                'email' => $employee->email,
                'position_id' => $employee->position_id,
                'position_name' => $employee->position?->name,
                'office_id' => $employee->office_id,
                'office_name' => $employee->office?->name,
                'status_id' => $employee->status_id,
                'status_name' => $employee->status?->name,
                'objects_count' => 0, // TODO: связь с объектами
                'clients_count' => 0, // TODO: связь с клиентами
                'success_deals' => 0, // TODO: связь со сделками
                'failed_deals' => 0,  // TODO: связь со сделками
                'active_until' => $employee->active_until?->format('d.m.Y'),
                'active_until_time' => $employee->active_until?->format('H:i'),
                'is_active' => $employee->is_active,
                'positions' => $positions,
                'offices' => $offices,
            ];
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Создание сотрудника (AJAX)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:50',
            'birthday' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
            'office_id' => 'nullable|exists:company_offices,id',
            'position_id' => 'nullable|exists:dictionaries,id',
            'status_id' => 'nullable|exists:dictionaries,id',
            'tag_ids' => 'nullable|array',
            'passport' => 'nullable|string|max:50',
            'inn' => 'nullable|string|max:20',
            'comment' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'active_until' => 'nullable|date',
        ]);

        // Создание пользователя
        $user = User::create([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
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
            'success' => true,
            'message' => 'Сотрудник создан',
            'employee' => $employee,
        ]);
    }

    /**
     * Получение данных сотрудника (AJAX)
     */
    public function show(Employee $employee): JsonResponse
    {
        $employee->load(['company', 'office', 'position', 'status']);

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'middle_name' => $employee->middle_name,
                'full_name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'birthday' => $employee->birthday?->format('Y-m-d'),
                'company_id' => $employee->company_id,
                'company_name' => $employee->company?->name,
                'office_id' => $employee->office_id,
                'office_name' => $employee->office?->name,
                'position_id' => $employee->position_id,
                'position_name' => $employee->position?->name,
                'status_id' => $employee->status_id,
                'status_name' => $employee->status?->name,
                'tag_ids' => $employee->tag_ids,
                'passport' => $employee->passport,
                'inn' => $employee->inn,
                'comment' => $employee->comment,
                'photo_url' => $employee->photo_url,
                'active_until' => $employee->active_until?->format('Y-m-d H:i'),
                'is_active' => $employee->is_active,
            ],
        ]);
    }

    /**
     * Обновление сотрудника (AJAX)
     */
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'birthday' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
            'office_id' => 'nullable|exists:company_offices,id',
            'position_id' => 'nullable|exists:dictionaries,id',
            'status_id' => 'nullable|exists:dictionaries,id',
            'tag_ids' => 'nullable|array',
            'passport' => 'nullable|string|max:50',
            'inn' => 'nullable|string|max:20',
            'comment' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'active_until' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Загрузка нового фото
        if ($request->hasFile('photo')) {
            // Удаление старого фото
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Сотрудник обновлен',
            'employee' => $employee,
        ]);
    }

    /**
     * Удаление сотрудника
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
     * Обновление должности (AJAX из таблицы)
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
     * Обновление офиса (AJAX из таблицы)
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
     * Поиск сотрудников (для Select2)
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        $search = $request->input('q', '');

        $employees = Employee::query()
            ->active()
            ->when($search, fn($q) => $q->search($search))
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'phone']);

        $results = $employees->map(fn($e) => [
            'id' => $e->id,
            'text' => $e->full_name . ($e->phone ? " ({$e->phone})" : ''),
        ]);

        return response()->json(['results' => $results]);
    }
}
