<?php

namespace App\Services\XmlExport\Adapters;

use App\Services\XmlExport\Dto\PropertyExportData;
use App\Services\XmlExport\Mappings\RemMappings;
use Spatie\ArrayToXml\ArrayToXml;

class RemAdapter extends AbstractXmlAdapter
{
    private const FEED_ID = 40;
    private const BASE_URL = 'https://rem.ua/feed/find/';

    public function getName(): string
    {
        return 'rem';
    }

    public function getRootElement(): string
    {
        return 'offer';
    }

    public function validate(PropertyExportData $dto): array
    {
        $missing = [];

        // Тип сделки — проверяем замапленное значение, а не сырое
        if (!RemMappings::mapDealType($dto->dealTypeName)) $missing[] = 'type';
        // Категория — проверяем замапленное значение
        if (!RemMappings::mapCategory($dto->propertyTypeName)) $missing[] = 'category';
        // Title: сначала из translations, потом fallback на buildTitle()
        if (!$dto->title && !RemMappings::buildTitle($dto->dealTypeName, $dto->propertyTypeName)) $missing[] = 'title';

        if (empty($dto->createdAt)) $missing[] = 'creation-date';
        if (empty($dto->updatedAt)) $missing[] = 'update-time';

        // Локация
        if (empty($dto->countryName)) $missing[] = 'location.country';
        if (empty($dto->stateName)) $missing[] = 'location.region';
        if (empty($dto->cityName)) $missing[] = 'location.locality-name';
        if (empty($dto->districtName)) $missing[] = 'location.district';
        if (empty($dto->zoneName)) $missing[] = 'location.sub-locality-name';
        if (empty($dto->streetName) && empty($dto->buildingNumber)) $missing[] = 'location.address';

        // Агент
        if (empty($dto->employeeName)) $missing[] = 'sales-agent.name';
        if (empty($dto->phone)) $missing[] = 'sales-agent.phone';
        if (empty($dto->employeeId)) $missing[] = 'sales-agent.id';

        // Цена
        if (empty($dto->price)) $missing[] = 'price.value';
        if (empty($dto->currencyCode)) $missing[] = 'price.currency';

        // Площадь
        if (empty($dto->areaTotal)) $missing[] = 'area.value';

        // Параметры
        if (empty($dto->roomCountValue)) $missing[] = 'rooms';
        if (empty($dto->floor)) $missing[] = 'floor';
        if (empty($dto->floorsTotal)) $missing[] = 'floor_count';

        // Фото (минимум 1)
        if (empty($dto->photoUrls)) $missing[] = 'image (минимум 1 фото)';

        return $missing;
    }

    public function toArray(PropertyExportData $dto): array
    {
        $address = $this->buildAddress($dto->streetName, $dto->buildingNumber);

        $category = RemMappings::mapCategory($dto->propertyTypeName);
        $dealType = RemMappings::mapDealType($dto->dealTypeName);
        $urlSegment = RemMappings::mapUrlSegment($category, $dealType);

        $data = [
            '_attributes' => ['internal-id' => $dto->id],
            'url'           => $urlSegment
                ? self::BASE_URL . self::FEED_ID . '/' . $urlSegment . '/' . $dto->id
                : null,
            'type'          => RemMappings::mapDealType($dto->dealTypeName),
            'title'         => $dto->title ?: RemMappings::buildTitle($dto->dealTypeName, $dto->propertyTypeName),
            'description'   => $dto->description ?: 'Выгодное предложение',
            'property-type' => 'жилая',
            'category'      => RemMappings::mapCategory($dto->propertyTypeName),
            'creation-date' => $dto->createdAt,
            'update-time'   => $dto->updatedAt,

            'location' => [
                'country'            => $dto->countryName,
                'region'             => $dto->stateName,
                'locality-name'      => $dto->cityName,
                'district'           => $dto->districtName,
                'address'            => $address,
                'apartment'          => $dto->apartmentNumber ?: '0',
                'sub-locality-name'  => $dto->zoneName,
            ],

            'sales-agent' => [
                'name'  => $dto->employeeName,
                'phone' => $dto->phone,
                'id'    => $dto->employeeId,
            ],

            'price' => [
                'value'    => $dto->price ? (int) $dto->price : null,
                'currency' => RemMappings::mapCurrency($dto->currencyCode),
            ],

            'area' => [
                'value' => $dto->areaTotal,
            ],

            'living-space' => [
                'value' => $dto->areaLiving,
            ],

            'kitchen-space' => [
                'value' => $dto->areaKitchen,
            ],

            'lot-area' => [
                'value' => $dto->areaLand,
            ],

            'image'        => $dto->photoUrls,
            'rooms'        => $dto->roomCountValue ? (int) $dto->roomCountValue : null,
            'floor'        => $dto->floor,
            'floor_count'  => $dto->floorsTotal,
            'is_premium'   => 'false',
            'quality'      => $dto->conditionName,
            'video-review' => $dto->youtubeUrl,
        ];

        return $data;
    }

    /**
     * @param PropertyExportData[] $items
     */
    public function generateBatchXml(array $items): string
    {
        $batchData = [];

        foreach ($items as $dto) {
            $batchData[] = $this->filterEmpty($this->toArray($dto));
        }

        return ArrayToXml::convert(
            [
                'generation-date' => now()->format('d.m.Y H:i'),
                'offer'           => $batchData,
            ],
            [
                'rootElementName' => 'realty-feed',
                '_attributes' => [
                    'xmlns' => 'http://webmaster.yandex.ru/schemas/feed/realty/2010-06',
                ],
            ],
            xmlEncoding: 'UTF-8',
        );
    }

    private function buildAddress(?string $streetName, ?string $buildingNumber): ?string
    {
        $parts = array_filter([$streetName, $buildingNumber]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }
}
