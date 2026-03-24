<?php

namespace App\Helpers;

class PhoneFormatter
{
    /**
     * Форматирование телефонного номера для хранения в БД.
     * Украинские номера: +38 (0XX) XXX-XX-XX
     * Остальные страны: E.164 (+XXXXXXXXXXXX)
     */
    public static function format(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return $phone;
        }

        if (self::isUkrainian($digits)) {
            return self::formatUkrainian($digits);
        }

        // E.164 для остальных стран
        return '+' . $digits;
    }

    private static function isUkrainian(string $digits): bool
    {
        // 380XXXXXXXXX (12 цифр)
        if (str_starts_with($digits, '380') && strlen($digits) === 12) {
            return true;
        }

        // 80XXXXXXXXX (11 цифр, старый формат без 3)
        if (str_starts_with($digits, '80') && strlen($digits) === 11) {
            return true;
        }

        // 0XXXXXXXXX (10 цифр, локальный формат)
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return true;
        }

        // 9 цифр без ведущего 0 (абонентский номер)
        if (!str_starts_with($digits, '0') && strlen($digits) === 9) {
            return true;
        }

        return false;
    }

    private static function formatUkrainian(string $digits): string
    {
        // Нормализуем до абонентского номера (9 цифр после 0)
        if (str_starts_with($digits, '380')) {
            $subscriber = substr($digits, 3); // 9 цифр
        } elseif (str_starts_with($digits, '80') && strlen($digits) === 11) {
            $subscriber = substr($digits, 2); // 80XXXXXXXXX → 9 цифр
        } elseif (str_starts_with($digits, '0')) {
            $subscriber = substr($digits, 1); // 9 цифр
        } else {
            $subscriber = $digits;
        }

        if (strlen($subscriber) !== 9) {
            return '+380' . $subscriber;
        }

        // Формат: +38 (0XX) XXX-XX-XX
        $areaCode = substr($subscriber, 0, 2);
        $part1 = substr($subscriber, 2, 3);
        $part2 = substr($subscriber, 5, 2);
        $part3 = substr($subscriber, 7, 2);

        return "+38 (0{$areaCode}) {$part1}-{$part2}-{$part3}";
    }
}
