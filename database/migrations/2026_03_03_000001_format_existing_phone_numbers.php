<?php

use App\Helpers\PhoneFormatter;
use App\Models\Contact\ContactPhone;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ContactPhone::query()->each(function (ContactPhone $phone) {
            $formatted = PhoneFormatter::format($phone->phone);
            if ($formatted !== $phone->phone) {
                $phone->update(['phone' => $formatted]);
            }
        });
    }

    public function down(): void
    {
        // Невозможно надёжно откатить форматирование
    }
};
