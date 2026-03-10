<?php

namespace App\Http\Controllers\Company\Company\Actions;

use App\Models\Reference\Company;
use Illuminate\Support\Facades\Storage;

/**
 * Бизнес-логика удаления компании.
 *
 * Удаляет логотип из storage и саму компанию.
 * Офисы удаляются каскадно благодаря FK.
 */
class DeleteCompany
{
    /**
     * Удалить компанию и связанные файлы.
     *
     * @param Company $company Удаляемая компания
     * @return void
     */
    public function execute(Company $company): void
    {
        // Удаляем логотип из storage
        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
        }

        // Офисы удалятся каскадно благодаря FK
        $company->delete();
    }
}
