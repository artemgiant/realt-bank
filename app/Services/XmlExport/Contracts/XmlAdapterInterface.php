<?php

namespace App\Services\XmlExport\Contracts;

use App\Services\XmlExport\Dto\PropertyExportData;

interface XmlAdapterInterface
{
    public function getName(): string;

    public function getRootElement(): string;

    public function toArray(PropertyExportData $dto): array;

    public function generateXml(PropertyExportData $dto): string;

    /**
     * @param PropertyExportData[] $items
     */
    public function generateBatchXml(array $items): string;
}
