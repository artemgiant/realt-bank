<?php

namespace App\Services\XmlExport\Adapters;

use App\Services\XmlExport\Dto\PropertyExportData;
use App\Services\XmlExport\Mappings\DimRiaMappings;

class DimRiaAdapter extends AbstractXmlAdapter
{
    public function getName(): string
    {
        return 'dim_ria';
    }

    public function getRootElement(): string
    {
        return 'realty';
    }

    public function toArray(PropertyExportData $dto): array
    {
        $features = DimRiaMappings::mapFeatures($dto->featureNames);
        $heatingFields = DimRiaMappings::mapHeatingFields($dto->heatingTypeName);

        return [
            // Базовые параметры (константы)
            'advert_type'     => 'Продажа',
            'realty_type'     => 'Квартира',
            'realty_sale_type' => 1,

            // Данные риелтора
            'email'           => $dto->email,
            'phone'           => $dto->phone,
            'local_realty_id' => $dto->id,

            // Фото
            'photos_urls' => [
                'loc' => $dto->photoUrls,
            ],

            // Видео
            'youtube_link' => $dto->youtubeUrl,
            'tiktok_link'  => $dto->tiktokUrl,

            // Расположение
            'state'            => $dto->stateName,
            'city'             => $dto->cityName,
            'district'         => $dto->districtName,
            'street'           => $dto->streetName,
            'street_type'      => 'улица',
            'building_number'  => $dto->buildingNumber,
            'show_building_no' => 1,
            'show_flat_no'     => 1,
            'flat_number_str'  => $dto->apartmentNumber,
            'longitude'        => $dto->longitude,
            'latitude'         => $dto->latitude,
            'radius_location'  => 'да',

            // Характеристики
            'characteristics' => array_merge(
                [
                    // Информация о доме
                    'wall_type'   => DimRiaMappings::mapWallType($dto->wallTypeName),
                    'build_year'  => $dto->yearBuilt,
                    'parking'     => DimRiaMappings::mapParking($dto->featureNames),

                    // Основные параметры
                    'rooms_count'    => $dto->roomCountValue,
                    'total_area'     => $dto->areaTotal,
                    'living_area'    => $dto->areaLiving,
                    'kitchen_area'   => $dto->areaKitchen,
                    'floors'         => $dto->floorsTotal,
                    'floor'          => $dto->floor,
                    'flat_state'     => DimRiaMappings::mapCondition($dto->conditionName),
                    'ceiling_height' => $dto->ceilingHeightValue,

                    // Тип дома
                    'building_type' => DimRiaMappings::mapBuildingType($dto->buildingTypeName),

                    // Цена
                    'price_type'  => 'за объект',
                    'price'       => $dto->price ? (int) $dto->price : null,
                    'currency'    => DimRiaMappings::mapCurrency($dto->currencySymbol),
                    'offer_type'  => 'от посредника',
                ],
                // Отопление (да/нет поля)
                $heatingFields,
                // Features → boolean поля (да/нет)
                $features,
            ),
        ];
    }
}
