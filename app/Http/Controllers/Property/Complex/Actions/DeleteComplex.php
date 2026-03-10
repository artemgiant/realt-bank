<?php

namespace App\Http\Controllers\Property\Complex\Actions;

use App\Models\Reference\Complex;
use Illuminate\Support\Facades\Storage;

/**
 * Бизнес-логика удаления комплекса.
 *
 * Удаляет все файлы (логотип, фото, планы, планы блоков),
 * директорию комплекса и саму запись.
 */
class DeleteComplex
{
    /** Удалить комплекс и все связанные файлы */
    public function execute(Complex $complex): void
    {
        // Удалить логотип
        if ($complex->logo_path) {
            Storage::disk('public')->delete($complex->logo_path);
        }

        // Удалить фото из JSON
        if ($complex->photos) {
            foreach ($complex->photos as $photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
        }

        // Удалить планы из JSON
        if ($complex->plans) {
            foreach ($complex->plans as $planPath) {
                Storage::disk('public')->delete($planPath);
            }
        }

        // Удалить планы блоков
        foreach ($complex->blocks as $block) {
            if ($block->plan_path) {
                Storage::disk('public')->delete($block->plan_path);
            }
        }

        // Удалить директорию комплекса
        Storage::disk('public')->deleteDirectory("complexes/{$complex->id}");

        $complex->delete();
    }
}
