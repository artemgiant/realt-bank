<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Отображение списка сотрудников
     */
    public function index()
    {
        return view('pages.employees.index');
    }
}
