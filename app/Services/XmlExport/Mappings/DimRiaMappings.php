<?php

namespace App\Services\XmlExport\Mappings;

use Illuminate\Support\Collection;

class DimRiaMappings
{
    /**
     * Dictionary wall_type name → DIM.RIA wall_type value
     */
    public const WALL_TYPE_MAP = [
        'Кирпич'                => 'кирпич',
        'Силикатный кирпич'     => 'кирпич',
        'Керамический кирпич'   => 'кирпич',
        'Облицовочный кирпич'   => 'кирпич',
        'Панель'                => 'панель',
        'Монолит'               => 'монолит',
        'Монолитный железобетон'=> 'монолит',
        'Пеноблок'              => 'пеноблок',
        'Газоблок'              => 'газобетон',
        'Монолитно-кирпичный'   => 'монолитно-кирпичный',
        'Монолитно-блочный'     => 'монолитно-кирпичный',
        'Монолитно-каркасный'   => 'монолитно-кирпичный',
        'Блочно-кирпичный'      => 'монолитно-кирпичный',
        'Керамический блок'     => 'керамический блок',
        'СИП'                   => 'СИП',
        'Каркасно-каменная'     => 'каркасный',
        'Бескаркасная'          => 'каркасный',
        'Дерево и кирпич'       => 'кирпич',
        'Ракушечник (ракушняк)' => 'кирпич',
        'Керамзитобетон'        => 'пеноблок',
    ];

    /**
     * Dictionary condition name → DIM.RIA flat_state value
     */
    public const CONDITION_MAP = [
        'С ремонтом'     => 'евроремонт',
        'Жилая'          => 'хорошее',
        'От строителей'  => 'без отделочных работ',
        'Без ремонта'    => 'требует ремонта / без ремонта / ремонт не закончен',
    ];

    /**
     * Dictionary building_type name → DIM.RIA building_type value
     */
    public const BUILDING_TYPE_MAP = [
        'Новострой'          => 'новый фонд',
        'Хрущевка'           => 'хрущевка',
        'Чешка'              => 'чешка',
        'Гостинка'           => 'гостинка',
        'Малосемейка'        => 'малосемейка',
        'Сталинка'           => 'сталинка',
        'Спецпроект'         => 'индивидуальный проект',
        'Бельгийка'          => 'другое',
        'Старый фонд'        => 'дореволюционный',
        'Квартира на земле'  => 'другое',
        'Московка'           => 'другое',
        'Сотовый'            => 'другое',
        'Харьковка'          => 'другое',
        'Югославка'          => 'другое',
        'Общежитие'          => 'другое',
        'Здание'             => 'другое',
        'Болгарка'           => 'другое',
    ];

    /**
     * Dictionary heating_type name → DIM.RIA heating field mapping
     * Key = our Dictionary name, Value = DIM.RIA XML field name (inside characteristics)
     */
    public const HEATING_TYPE_MAP = [
        'Индивидуальное электрическое отопление' => 'individual_electricity',
        'Твердотопливное отопление'              => 'solid_fuel_boiler',
        'Без отопления'                          => 'without_heating',
    ];

    /**
     * Dictionary feature name → DIM.RIA XML field name
     * Used for boolean (да/нет) fields in characteristics
     */
    public const FEATURE_MAP = [
        // Особенности планировки
        'Двухуровневая'         => 'multi_level_feature',
        'Балкон'                => 'balcony_loggia',
        'Лоджия'                => 'balcony_loggia',
        'Гараж'                 => 'utp_with_garage',

        // Инфраструктура
        'Паркинг'               => 'utp_reserved_parking_space',
        'Охрана'                => 'utp_protected_area',
        'Закрытая территория'   => 'utp_protected_area',
        'Видеонаблюдение'       => 'utp_protected_area',
        'Вид на море'           => 'utp_beautiful_view',
        'Вид на парк'           => 'utp_near_the_park',
        'Панорамные окна'       => 'utp_beautiful_view',
    ];

    /**
     * Feature name → DIM.RIA parking value
     */
    public const PARKING_MAP = [
        'Паркинг' => 'наземный паркинг',
        'Гараж'   => 'гараж',
    ];

    /**
     * Currency symbol mapping (на случай несовпадения)
     */
    public const CURRENCY_MAP = [
        '$'  => '$',
        '₴'  => 'грн',
        '€'  => '€',
        'грн'=> 'грн',
    ];

    public static function mapWallType(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::WALL_TYPE_MAP[$name] ?? null;
    }

    public static function mapCondition(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::CONDITION_MAP[$name] ?? null;
    }

    public static function mapBuildingType(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::BUILDING_TYPE_MAP[$name] ?? null;
    }

    public static function mapCurrency(?string $symbol): ?string
    {
        if ($symbol === null) {
            return null;
        }

        return self::CURRENCY_MAP[$symbol] ?? $symbol;
    }

    /**
     * Map parking from feature names
     */
    public static function mapParking(array $featureNames): ?string
    {
        foreach (self::PARKING_MAP as $feature => $dimRiaValue) {
            if (in_array($feature, $featureNames)) {
                return $dimRiaValue;
            }
        }

        return null;
    }

    /**
     * Map heating type to DIM.RIA field
     * Returns [field_name => 'да'] or empty array
     */
    public static function mapHeatingFields(?string $heatingTypeName): array
    {
        if ($heatingTypeName === null) {
            return [];
        }

        $field = self::HEATING_TYPE_MAP[$heatingTypeName] ?? null;

        if ($field === null) {
            return [];
        }

        return [$field => 'да'];
    }

    /**
     * Map features to DIM.RIA boolean fields
     * Returns [field_name => 'да', ...]
     */
    public static function mapFeatures(array $featureNames): array
    {
        $result = [];

        foreach ($featureNames as $name) {
            $field = self::FEATURE_MAP[$name] ?? null;
            if ($field !== null) {
                $result[$field] = 'да';
            }
        }

        return $result;
    }
}
