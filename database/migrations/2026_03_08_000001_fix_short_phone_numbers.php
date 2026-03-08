<?php

use App\Helpers\PhoneFormatter;
use App\Models\Contact\ContactPhone;
use App\Models\Employee\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Исправляем короткие украинские номера (добавляем недостающие цифры)
        // и форматируем все номера через PhoneFormatter

        // 1. Employees
        Employee::query()->whereNotNull('phone')->each(function (Employee $employee) {
            $phone = $this->fixShortUkrainianNumber($employee->phone);
            $formatted = PhoneFormatter::format($phone);
            if ($formatted !== $employee->phone) {
                $employee->updateQuietly(['phone' => $formatted]);
            }
        });

        // 2. Contact phones
        ContactPhone::query()->each(function (ContactPhone $contactPhone) {
            $phone = $this->fixShortUkrainianNumber($contactPhone->phone);
            $formatted = PhoneFormatter::format($phone);
            if ($formatted !== $contactPhone->phone) {
                $contactPhone->updateQuietly(['phone' => $formatted]);
            }
        });
    }

    /**
     * Дополняем короткие украинские номера случайными цифрами до 12 цифр.
     * Например: +3809509022 (10 цифр) → +380950902234 (12 цифр)
     */
    private function fixShortUkrainianNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Только номера начинающиеся на 380, но короче 12 цифр
        if (str_starts_with($digits, '380') && strlen($digits) > 3 && strlen($digits) < 12) {
            $missing = 12 - strlen($digits);
            $digits .= str_pad('', $missing, (string) rand(0, 9));
            for ($i = strlen($digits) - $missing; $i < strlen($digits); $i++) {
                $digits[$i] = (string) rand(0, 9);
            }
            return '+' . $digits;
        }

        return $phone;
    }

    public function down(): void
    {
        // Невозможно надёжно откатить — оригинальные номера были некорректными
    }
};
