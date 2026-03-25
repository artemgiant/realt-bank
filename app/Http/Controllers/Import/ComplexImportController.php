<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use App\Models\Reference\Developer;
use App\Services\Import\ComplexImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplexImportController extends Controller
{
    /**
     * Страница загрузки файла
     */
    public function index(): View
    {
        $counts = [
            'developers' => Developer::count(),
            'complexes' => Complex::count(),
            'blocks' => Block::count(),
        ];

        // Уникальные значения source из всех трёх таблиц
        $sources = Developer::whereNotNull('source')->distinct()->pluck('source')
            ->merge(Complex::whereNotNull('source')->distinct()->pluck('source'))
            ->merge(Block::whereNotNull('source')->distinct()->pluck('source'))
            ->unique()
            ->sort()
            ->values();

        return view('pages.import.complexes.index', compact('counts', 'sources'));
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

    /**
     * Очистка данных (blocks, complexes, developers)
     */
    public function clear(Request $request): RedirectResponse
    {
        $request->validate([
            'source' => 'nullable|string|max:50',
        ]);

        $source = $request->input('source');

        $blockQuery = Block::query();
        $complexQuery = Complex::query();
        $developerQuery = Developer::query();

        if ($source) {
            $blockQuery->where('source', $source);
            $complexQuery->where('source', $source);
            $developerQuery->where('source', $source);
        }

        $deleted = [
            'blocks' => $blockQuery->count(),
            'complexes' => $complexQuery->count(),
            'developers' => $developerQuery->count(),
            'source' => $source,
        ];

        // Удаляем физически (forceDelete из-за SoftDeletes, иначе unique index блокирует повторный импорт)
        $blockQuery->forceDelete();
        $complexQuery->forceDelete();
        $developerQuery->forceDelete();

        return redirect()->route('import.complexes.index')
            ->with('cleared', $deleted);
    }
}
