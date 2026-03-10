<?php

namespace App\Http\Controllers\Contact\Actions;

use App\Models\Contact\Contact;
use Illuminate\Support\Facades\Storage;

/**
 * Бизнес-логика удаления контакта.
 *
 * Удаляет фото из хранилища и саму запись контакта.
 */
class DeleteContact
{
    /**
     * Удалить контакт и его фото из хранилища.
     *
     * @param Contact $contact Удаляемый контакт
     * @return void
     */
    public function execute(Contact $contact): void
    {
        // Удаляем фото из хранилища
        if ($contact->photo) {
            Storage::disk('public')->delete($contact->photo);
        }

        $contact->delete();
    }
}
