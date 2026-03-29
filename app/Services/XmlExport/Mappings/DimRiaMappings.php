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
     * Deal type name → DIM.RIA advert_type value
     * Маппинг по началу строки (str_starts_with)
     */
    public const ADVERT_TYPE_MAP = [
        'Продажа'          => 'Продажа',
        'Долгосрочная'     => 'Долгосрочная аренда',
        'Аренда посуточная'=> 'Аренда посуточная',
    ];

    /**
     * Property type name → DIM.RIA realty_type value
     */
    public const REALTY_TYPE_MAP = [
        // Комнаты
        'Комната'                           => 'Комната',
        // Квартиры
        'Квартира'                          => 'Квартира',
        'Пентхаус'                          => 'Квартира',
        'Студия'                            => 'Квартира',
        'Апартаменты'                       => 'Квартира',
        'Квартира на земле'                 => 'Квартира',
        // Дома
        'Дом'                               => 'Частный дом',
        'Таунхаус'                          => 'Таунхаус',
        'Дуплекс'                           => 'Дуплекс',
        'Часть дома'                        => 'Часть дома',
        'Коттедж'                           => 'Частный дом',
        'Вилла'                             => 'Частный дом',
        'Дача'                              => 'Частный дом',
        // Участки
        'Земля под жилую застройку'         => 'Участок под жилую застройку',
        'Земля под садоводство'             => 'Земля коммерческого назначение',
        'Земля коммерческого назначения'    => 'Земля коммерческого назначение',
        'Земля сельскохозяйственного назначения' => 'Земля сельскохозяйственного назначения',
        'Земля рекреационного назначения'   => 'Земля коммерческого назначение',
        // Коммерческая
        'Офисное помещение'                 => 'Офисное помещение',
        'Торговое помещение'                => 'Коммерческое помещение',
        'Ресторан/кафе'                     => 'Коммерческое помещение',
        'Гостиница/отель'                   => 'Коммерческое помещение',
        'Медицинское помещение'             => 'Коммерческое помещение',
        'Складские помещения'               => 'Коммерческое помещение',
        'Производственные помещения'        => 'Коммерческое помещение',
        'Здание'                            => 'Коммерческое помещение',
        'Готовый бизнес'                    => 'Коммерческое помещение',
        'Помещение свободного назначения'   => 'Коммерческое помещение',
        // Паркинг/гараж
        'Паркинг'                           => 'Подземный паркинг',
        'Машино-место'                      => 'Место на стоянке',
        'Гараж'                             => 'Отдельно стоящий гараж',
        'Бокс'                              => 'Место в гаражном кооперативе',
    ];

    /**
     * Property type name → category (для ветвления логики)
     */
    public const CATEGORY_MAP = [
        // Комнаты
        'Комната'                           => 'room',
        // Квартиры
        'Квартира'                          => 'apartment',
        'Пентхаус'                          => 'apartment',
        'Студия'                            => 'apartment',
        'Апартаменты'                       => 'apartment',
        'Квартира на земле'                 => 'apartment',
        // Дома
        'Дом'                               => 'house',
        'Таунхаус'                          => 'house',
        'Дуплекс'                           => 'house',
        'Часть дома'                        => 'house',
        'Коттедж'                           => 'house',
        'Вилла'                             => 'house',
        'Дача'                              => 'house',
        // Участки
        'Земля под жилую застройку'         => 'land',
        'Земля под садоводство'             => 'land',
        'Земля коммерческого назначения'    => 'land',
        'Земля сельскохозяйственного назначения' => 'land',
        'Земля рекреационного назначения'   => 'land',
        // Коммерческая
        'Офисное помещение'                 => 'commercial',
        'Торговое помещение'                => 'commercial',
        'Ресторан/кафе'                     => 'commercial',
        'Гостиница/отель'                   => 'commercial',
        'Медицинское помещение'             => 'commercial',
        'Складские помещения'               => 'commercial',
        'Производственные помещения'        => 'commercial',
        'Здание'                            => 'commercial',
        'Готовый бизнес'                    => 'commercial',
        'Помещение свободного назначения'   => 'commercial',
        // Паркинг/гараж
        'Паркинг'                           => 'garage',
        'Машино-место'                      => 'garage',
        'Гараж'                             => 'garage',
        'Бокс'                              => 'garage',
    ];

    /**
     * Минимум фото по категории
     */
    public const MIN_PHOTOS_MAP = [
        'apartment'  => 5,
        'room'       => 5,
        'house'      => 5,
        'land'       => 1,
        'commercial' => 3,
        'garage'     => 3,
    ];

    /**
     * Dictionary condition name → DOM.RIA house_state (для домов)
     */
    public const HOUSE_STATE_MAP = [
        'С ремонтом'     => 'отличное',
        'Жилая'          => 'хорошее',
        'От строителей'  => 'без отделки / черновая отделка',
        'Без ремонта'    => 'требует ремонта',
    ];

    /**
     * Dictionary condition name → DOM.RIA condition_of_building_repair (для коммерческой)
     */
    public const COMMERCIAL_CONDITION_MAP = [
        'С ремонтом'     => 'отличное',
        'Жилая'          => 'хорошее',
        'От строителей'  => 'без отделки / черновая отделка',
        'Без ремонта'    => 'требует ремонта',
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

    public static function mapCategory(?string $propertyTypeName): ?string
    {
        if ($propertyTypeName === null) {
            return null;
        }

        return self::CATEGORY_MAP[$propertyTypeName] ?? null;
    }

    public static function minPhotos(string $category): int
    {
        return self::MIN_PHOTOS_MAP[$category] ?? 5;
    }

    public static function mapHouseState(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::HOUSE_STATE_MAP[$name] ?? null;
    }

    public static function mapCommercialCondition(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::COMMERCIAL_CONDITION_MAP[$name] ?? null;
    }

    public static function mapAdvertType(?string $dealTypeName): ?string
    {
        if ($dealTypeName === null) {
            return null;
        }

        foreach (self::ADVERT_TYPE_MAP as $prefix => $advertType) {
            if (str_starts_with($dealTypeName, $prefix)) {
                return $advertType;
            }
        }

        return null;
    }

    public static function mapRealtyType(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::REALTY_TYPE_MAP[$name] ?? null;
    }

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
