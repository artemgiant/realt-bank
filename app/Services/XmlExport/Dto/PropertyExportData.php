<?php

namespace App\Services\XmlExport\Dto;

use App\Models\Property\Property;

class PropertyExportData
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $email,
        public readonly ?string $phone,

        // Location
        public readonly ?string $stateName,
        public readonly ?string $cityName,
        public readonly ?string $districtName,
        public readonly ?string $streetName,
        public readonly ?string $buildingNumber,
        public readonly ?string $apartmentNumber,
        public readonly ?float $latitude,
        public readonly ?float $longitude,

        // Dictionary references
        public readonly ?string $wallTypeName,
        public readonly ?string $buildingTypeName,
        public readonly ?string $conditionName,
        public readonly ?string $heatingTypeName,
        public readonly ?string $roomCountValue,
        public readonly ?string $ceilingHeightValue,

        // Characteristics
        public readonly ?float $areaTotal,
        public readonly ?float $areaLiving,
        public readonly ?float $areaKitchen,
        public readonly ?int $floor,
        public readonly ?int $floorsTotal,
        public readonly ?int $yearBuilt,

        // Price
        public readonly ?float $price,
        public readonly ?string $currencySymbol,

        // Media
        public readonly ?string $youtubeUrl,
        public readonly ?string $tiktokUrl,
        public readonly array $photoUrls,

        // Features (array of feature names)
        public readonly array $featureNames,
    ) {}

    public static function fromModel(Property $property): self
    {
        return new self(
            id: $property->id,
            email: $property->employee?->email,
            phone: $property->employee?->phone,

            stateName: $property->state?->name,
            cityName: $property->city?->name,
            districtName: $property->district?->name,
            streetName: $property->street?->name,
            buildingNumber: $property->building_number,
            apartmentNumber: $property->apartment_number,
            latitude: $property->latitude ? (float) $property->latitude : null,
            longitude: $property->longitude ? (float) $property->longitude : null,

            wallTypeName: $property->wallType?->name,
            buildingTypeName: $property->buildingType?->name,
            conditionName: $property->condition?->name,
            heatingTypeName: $property->heatingType?->name,
            roomCountValue: $property->roomCount?->value,
            ceilingHeightValue: $property->ceilingHeight?->value,

            areaTotal: $property->area_total ? (float) $property->area_total : null,
            areaLiving: $property->area_living ? (float) $property->area_living : null,
            areaKitchen: $property->area_kitchen ? (float) $property->area_kitchen : null,
            floor: $property->floor,
            floorsTotal: $property->floors_total,
            yearBuilt: $property->year_built,

            price: $property->price ? (float) $property->price : null,
            currencySymbol: $property->currency?->symbol,

            youtubeUrl: $property->youtube_url,
            tiktokUrl: $property->tiktok_url,
            photoUrls: $property->photos->pluck('url')->toArray(),

            featureNames: $property->features->pluck('name')->toArray(),
        );
    }
}
