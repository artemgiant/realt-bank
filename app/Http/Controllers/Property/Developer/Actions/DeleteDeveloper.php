<?php

namespace App\Http\Controllers\Property\Developer\Actions;

use App\Models\Reference\Developer;
use Illuminate\Support\Facades\Storage;

/**
 * Бизнес-логика удаления девелопера.
 *
 * Удаляет логотип из storage и саму запись девелопера.
 */
class DeleteDeveloper
{
    /**
     * Удалить девелопера и связанные файлы.
     *
     * @param Developer $developer Удаляемый девелопер
     * @return void
     */
    public function execute(Developer $developer): void
    {
        // Удаляем логотип из storage
        if ($developer->logo_path) {
            Storage::disk('public')->delete($developer->logo_path);
        }

        // Удаляем запись девелопера
        $developer->delete();
    }
}
