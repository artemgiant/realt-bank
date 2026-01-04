<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Services\Import\ComplexImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplexImportController extends Controller
{
    /**
     * Страница загрузки файла
     */
    public function index(): View
    {
        return view('pages.import.complexes.index');
    }

    /**
     * Обработка импорта
     */
    public function import(Request $request, ComplexImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // max 10MB
        ], [
            'file.required' => 'Выберите файл для загрузки',
            'file.file' => 'Загруженный файл повреждён',
            'file.mimes' => 'Файл должен быть в формате Excel (xlsx, xls)',
            'file.max' => 'Размер файла не должен превышать 10MB',
        ]);

//        try {
            $result = $service->import($request->file('file'));

            return view('pages.import.complexes.result', compact('result'));
//        } catch (\Exception $e) {
//            return back()
//                ->withInput()
//                ->withErrors(['file' => 'Ошибка при обработке файла: ' . $e->getMessage()]);
//        }
    }
}
