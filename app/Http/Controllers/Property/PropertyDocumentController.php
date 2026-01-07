<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyDocumentController extends Controller
{
    /**
     * Скачивание документа по хешу
     */
    public function download(string $hash): StreamedResponse
    {
        // Находим документ по хешу
        $document = PropertyDocument::where('hash', $hash)->firstOrFail();

        // Проверяем права доступа (владелец объекта)
        if ($document->property->user_id !== auth()->id()) {
            abort(403, 'Нет доступа к этому документу');
        }

        // Проверяем существование файла
        if (!Storage::exists($document->path)) {
            abort(404, 'Файл не найден');
        }

        return Storage::download($document->path, $document->filename);
    }

    /**
     * Удаление документа по хешу
     */
    public function destroy(string $hash)
    {
        // Находим документ по хешу
        $document = PropertyDocument::where('hash', $hash)->firstOrFail();

        // Проверяем права доступа
        if ($document->property->user_id !== auth()->id()) {
            abort(403, 'Нет доступа к этому документу');
        }

        // Удаляем файл из хранилища
        if (Storage::exists($document->path)) {
            Storage::delete($document->path);
        }

        // Удаляем запись из БД
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Документ удалён',
        ]);
    }
}
